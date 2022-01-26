<?php

namespace app\models\work;

use app\models\common\GroupErrors;
use app\models\common\LessonTheme;
use app\models\work\ErrorsWork;
use Yii;
use yii\helpers\Console;


class GroupErrorsWork extends GroupErrors
{

    public function GroupAmnesty ($modelGroupID)
    {
        $errors = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'amnesty' => null])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = 1;
            $err->save();
        }
    }

    private function NoAmnesty ($modelGroupID)
    {
        $errors = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'amnesty' => 1])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = null;
            $err->save();
        }
    }

    /*-------------------------------------------------*/

    private function CheckTeacher ($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 1])->all();
        $teacherCount = count(TeacherGroupWork::find()->where(['training_group_id' => $modelGroupID])->all());
        foreach ($err as $oneErr)
        {
            $start_time = $group->start_date;

            if ($teacherCount != 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
            else if ($start_time <= $now_time)
            {
                // в первого день занятия ещё нет препода? на кол!
                $oneErr->critical = 1;
                $oneErr->save();
            }
        }

        if ((count($err) == 0) && $teacherCount == 0)        // если не нашлась ошибка, то будем проверять с нуля
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 1;
            $this->time_start = date("Y.m.d H:i:s");
            if ($start_time <= $now_time)
                $this->critical = 1;
            $this->save();
        }
    }

    private function CheckOrder ($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 2])->all();
        $ordersCount = count(OrderGroupWork::find()->where(['training_group_id' => $modelGroupID])->all());
        $start_time = $group->start_date;
        $end_time = $group->finish_date;

        foreach ($err as $oneErr)
        {
            if ($ordersCount != 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
            else if ($end_time <= $now_time)
            {
                // тут должно быть повторное оповещание на почту что приказ должен быть добавлен в день последнего занятия
                $oneErr->critical = 1;
                $oneErr->save();
            }
        }

        if ((count($err) == 0)  && $ordersCount == 0 && $start_time <= $now_time)
        {
            // тут ещё должно быть 1 оповещение на почту
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 2;
            $this->time_start = date("Y.m.d H:i:s");
            if ($end_time <= $now_time)
                $this->critical = 1;
            $this->save();
        }
    }

    public function CheckOrderTrainingGroup ($groupsID)     // проверка всех групп которые были отмечены в образовательном приказе при его создании/редактировании
    {
        $now_time = date("Y-m-d");
        foreach ($groupsID as $groupID)
        {
            $group = TrainingGroupWork::find()->where(['id' => $groupID])->one();
            $this->CheckOrder($groupID, $group, $now_time);
        }
    }

    private function CheckPhotos ($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 3])->all();
        $end_time = $group->finish_date;

        foreach ($err as $oneErr)
        {
            if ($group->photos != null)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
            else if (date('Y-m-d', strtotime($end_time . '-7 day')) <= $now_time)
            {
                // тут должно быть повторное оповещание на почту что фотоматериалы добвляются за неделю до последнего занятия
                $oneErr->critical = 1;
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $group->photos == null && date('Y-m-d', strtotime($end_time . '-14 day')) <= $now_time)
        {
            // тут ещё должно быть 1 оповещение на почту
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 3;
            $this->time_start = date("Y.m.d H:i:s");
            if (date('Y-m-d', strtotime($end_time . '-7 day')) <= $now_time)
                $this->critical = 1;
            $this->save();
        }
    }

    private function CheckPresent($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 4])->all();
        $end_time = $group->finish_date;

        foreach ($err as $oneErr)
        {
            if ($group->present_data != null)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
            else if (date('Y-m-d', strtotime($end_time . '1 day')) <= $now_time)
            {
                // прошел день последнего занятия, а инфа не добавлена? на кол!
                $oneErr->critical = 1;
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $group->present_data == null && $end_time <= $now_time)
        {
            // тут ещё должно быть 1 оповещение на почту
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 4;
            $this->time_start = date("Y.m.d H:i:s");
            if (date('Y-m-d', strtotime($end_time . '1 day')) <= $now_time)
                $this->critical = 1;
            $this->save();
        }
    }

    private function CheckWork($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 5])->all();
        $end_time = $group->finish_date;

        foreach ($err as $oneErr)
        {
            if ($group->work_data != null)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
            else if (date('Y-m-d', strtotime($end_time . '1 day')) <= $now_time)
            {
                // прошел день последнего занятия, а инфа не добавлена? на кол!
                $oneErr->critical = 1;
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $group->work_data == null && $end_time <= $now_time)
        {
            // тут ещё должно быть 1 оповещение на почту
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 5;
            $this->time_start = date("Y.m.d H:i:s");
            if (date('Y-m-d', strtotime($end_time . '1 day')) <= $now_time)
                $this->critical = 1;
            $this->save();
        }
    }

    private function CheckCapacity($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 6])->all();
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $modelGroupID])->all();
        $lessonsCount = count($lessons);
        $capacity = TrainingProgramWork::find()->where(['id' => $group->training_program_id])->one()->capacity;
        $end_time = $group->finish_date;

        foreach ($err as $oneErr)
        {
            if ($lessonsCount == $capacity)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
            else if ($end_time <= $now_time)
            {
                // на кол!
                $oneErr->critical = 1;
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $lessonsCount != $capacity)
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 6;
            $this->time_start = date("Y.m.d H:i:s");
            if ($end_time <= $now_time)
                $this->critical = 1;
            $this->save();
        }
    }

    private function CheckCertificate ($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 8])->all();
        $end_time = $group->finish_date;
        $certificats = TrainingGroupParticipantWork::find()->where(['training_group_id' => $modelGroupID, 'status' => 0])->all();
        $certificatCount = 0;

        foreach ($certificats as $certificat)
            if ($certificat->certificat_number  === null)
                $certificatCount++;

        foreach ($err as $oneErr)
        {
            if ($certificatCount == 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $certificatCount != 0 && $end_time <= $now_time)
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 8;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckAuditorium ($modelGroupID)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 14])->all();
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $modelGroupID])->all();
        $audsEducation = 1;

        foreach ($lessons as $lesson) {
            $audsLessons = $lesson->auditorium_id;
            $auditorium = AuditoriumWork::find()->where(['id' => $audsLessons])->one();
            if ($auditorium->is_education !== null && $auditorium->is_education == 0)
            {
                $audsEducation = 0;
                break;
            }
        }

        foreach ($err as $oneErr)
        {
            if ($audsEducation == 1)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }
        if (count($err) == 0 && $audsEducation == 0)
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 14;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function TwoTeachersOnePlace()
    {
        //$auditorium = TrainingGroupLessonWork::find()->select(['lesson_date', 'lesson_start_time', 'lesson_end_time', 'auditorium_id', 'training_group_id'])->
    }

    private function TwoPlacesOneTeacher($modelGroupID)
    {
        $teachers = TeacherGroupWork::find()->where(['training_group_id' => $modelGroupID])->all();
        foreach ($teachers as $teacher)
        {
            $lessons = TrainingGroupLessonWork::find()->joinWith(['lessonThemes lessonThemes'])->where(['lessonThemes.teacher_id' => $teacher])->all();
            $countLessons = count($lessons);
            $errorsGroup[] = '';
            for ($i = 0; $i < $countLessons - 1; $i++)
            {
                if (array_search($i, $errorsGroup) == false)
                    for ($j = $i+1; $j < $countLessons; $j++)
                    {
                        if ($lessons[$i]->lesson_date == $lessons[$j]->lesson_date && $lessons[$i]->auditorium_id == $lessons[$j]->auditorium_id)   // если дата и помещение совпали, то вероятно стоит время посмотреть
                        {
                            if (!($lessons[$i]->lesson_start_time > $lessons[$j]->lesson_end_time || $lessons[$j]->lesson_start_time > $lessons[$i]->lesson_end_time))
                            {
                                // если попали сюда, значит произошло наложение занятий
                                $errorsGroup += $i;
                                $errorsGroup += $j;
                            }
                        }
                    }
            }
        }


    }

    private function IncorrectDates($modelGroupID, $group)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 16])->all();
        $finishDate = $group->finish_date;
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $modelGroupID])->all();
        $check = 0;

        foreach ($lessons as $lesson)
        {
            if ($lesson->lesson_date > $finishDate)
            {
                $check = 1;
                break;
            }
        }

        foreach ($err as $oneErr)
        {
            if ($check == 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $check != 0)
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 16;
            $this->time_start = date("Y.m.d H:i:s");
            $this->critical = 1;
            $this->save();
        }
    }

    /*-------------------------------------------------*/

    public function CheckAuditoriumTrainingGroup ($modelAuditoriumID)   // для проверки при изменении типа помещения
    {
        $lessons = TrainingGroupLessonWork::find()->where(['auditorium_id' => $modelAuditoriumID])->groupBy(['training_group_id'])->all();
        $groupsId = [];
        foreach ($lessons as $lesson)
            $groupsId[] = $lesson->training_group_id;

        foreach ($groupsId as $groupId)
            $this->CheckAuditorium($groupId);
    }

    /*public function CheckSchedule ($modelGroupID)   // проверка всего что связано с расписанием учбеной группы
    {
        $group = TrainingGroupWork::find()->where(['id' => $modelGroupID])->one();
        $this->CheckAuditorium($modelGroupID);
        $this->IncorrectDates($modelGroupID, $group);
        $this->TwoPlacesOneTeacher($modelGroupID);
        $this->TwoTeachersOnePlace();
    }*/

    public function CheckErrorsTrainingGroup ($modelGroupID)    // проверка учебной группы на все ошибки (используется демоном)
    {
        $group = TrainingGroupWork::find()->where(['id' => $modelGroupID])->one();
        $now_time = date("Y-m-d");

        $this->CheckTeacher($modelGroupID, $group, $now_time);
        $this->CheckOrder($modelGroupID, $group, $now_time);
        $this->CheckPhotos($modelGroupID, $group, $now_time);
        $this->CheckPresent($modelGroupID, $group, $now_time);
        $this->CheckWork($modelGroupID, $group, $now_time);
        $this->CheckCapacity($modelGroupID, $group, $now_time);
        $this->CheckCertificate($modelGroupID, $group, $now_time);
        $this->CheckAuditorium($modelGroupID);
        $this->IncorrectDates($modelGroupID, $group);
        //$this->TwoPlacesOneTeacher($modelGroupID);
    }

    public function CheckErrorsTrainingGroupWithoutAmnesty ($modelGroupID)  // ручная проверка учебной группы при сохранении изменений (забываем амнистию ошибок)
    {
        $this->NoAmnesty($modelGroupID);
        $this->CheckErrorsTrainingGroup($modelGroupID);
        $this->CheckErrorsJournal($modelGroupID);
    }

    /*-------------------------------------------------*/

    private function CheckLesson ($modelGroupID, $lessons)  // проверка посещяемости
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 9])->all();

        $participantCount = count(TrainingGroupParticipantWork::find()->where(['training_group_id' => $modelGroupID])->all());

        $checkCount = 0;
        foreach ($lessons as $lesson)
        {
            $visits = VisitWork::find()->where(['training_group_lesson_id' => $lesson->id])->all();
            $count = 0;
            foreach ($visits as $visit)
            {
                if ($visit->status == 3)
                    $count++;
            }

            if ($count == $participantCount)
            {
                $checkCount = 1;
                if ($lesson->lesson_date < strtotime(date("Y-m-d") . '-3 day'))
                    $checkCount = 2;
                break;
            }
        }

        foreach ($err as $oneErr)
        {
            if ($checkCount == 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
            else if ($checkCount == 2)  // осталось мало времени для исправления
            {
                $oneErr->critical = 1;
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $checkCount != 0)
        {
            // значит кто-то детей не отмечал и на кол его посадить и письмо выслать
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 9;
            $this->time_start = date("Y.m.d H:i:s");
            if ($checkCount == 2)
                $this->critical = 1;
            $this->save();
        }
    }

    private function CheckTheme ($modelGroupID, $lessons)   // проверка заполненности тематического плана
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 15])->all();

        $checkCount = 0;
        foreach ($lessons as $lesson)
        {
            $theme = LessonThemeWork::find()->where(['training_group_lesson_id' => $lesson->id])->one();
            if ($theme === null || $theme->teacher_id === null)  // тут условие что или темы нет или поле препода пустое
            {
                $checkCount = 1;
                if ($lesson->lesson_date < strtotime(date("Y-m-d") . '-3 day'))
                    $checkCount = 2;
                break;
            }
        }

        foreach ($err as $oneErr)
        {
            if ($checkCount == 0)
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
            else if ($checkCount == 2)
            {
                $oneErr->critical = 1;
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $checkCount != 0)
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 15;
            $this->time_start = date("Y.m.d H:i:s");
            if ($checkCount == 2)
                $this->critical = 1;
            $this->save();
        }
    }

    public function CheckErrorsJournal ($modelGroupID)  // проверка ошибок связанных с электронным журналом
    {
        $now_time = date("Y-m-d");
        $finish_date = date('Y-m-d', strtotime($now_time . '-1 day'));
        $start_date = date('Y-m-d', strtotime($now_time . '-365 day'));
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $modelGroupID])->andWhere(['between', 'lesson_date', $start_date, $finish_date])->all();

        $this->CheckLesson($modelGroupID, $lessons);
        $this->CheckTheme($modelGroupID, $lessons);
    }

}
