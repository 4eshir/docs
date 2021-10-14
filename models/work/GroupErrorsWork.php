<?php

namespace app\models\work;

use app\models\common\GroupErrors;
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

    public function CheckTeacher ($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 1])->all();
        $teacherCount = count(TeacherGroupWork::find()->where(['training_group_id' => $modelGroupID])->all());
        $amnesty = 0;
        foreach ($err as $oneErr)
        {
            if ($oneErr->amnesty === null) // если она не прощена стоит посмотрить исправили её или стало только хуже
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
                    $oneErr->сritical = 1;
                    $oneErr->save();
                }
            }
            else $amnesty++;
        }

        if ((count($err) == 0 || count($err) == $amnesty) && $teacherCount == 0)        // если не нашлась ошибка, то будем проверять с нуля
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 1;
            $this->time_start = date("Y.m.d H:i:s");
            if ($start_time <= $now_time)
                $this->сritical = 1;
            $this->save();
        }
    }

    public function CheckOrder ($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 2])->all();
        $ordersCount = count(OrderGroupWork::find()->where(['training_group_id' => $modelGroupID])->all());
        $start_time = $group->start_date;
        $end_time = $group->finish_date;
        $amnesty = 0;

        foreach ($err as $oneErr)
        {
            if ($oneErr->amnesty === null)
            {
                if ($ordersCount != 0)     // ошибка исправлена
                {
                    $oneErr->time_the_end = date("Y.m.d H:i:s");
                    $oneErr->save();
                }
                else if ($end_time <= $now_time)
                {
                    // тут должно быть повторное оповещание на почту что приказ должен быть добавлен в день последнего занятия
                    $oneErr->сritical = 1;
                    $oneErr->save();
                }
            }
            else
                $amnesty++;
        }

        if ((count($err) == 0 || count($err) == $amnesty)  && $ordersCount == 0 && $start_time <= $now_time)
        {
            // тут ещё должно быть 1 оповещение на почту
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 2;
            $this->time_start = date("Y.m.d H:i:s");
            if ($end_time <= $now_time)
                $this->сritical = 1;
            $this->save();
        }
    }

    public function CheckPhotos ($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 3])->all();
        $end_time = $group->finish_date;
        $amnesty = 0;

        foreach ($err as $oneErr)
        {
            if ($oneErr->amnesty === null)
            {
                if ($group->photos != null)     // ошибка исправлена
                {
                    $oneErr->time_the_end = date("Y.m.d H:i:s");
                    $oneErr->save();
                }
                else if (date('Y-m-d', strtotime($end_time . '-7 day')) <= $now_time)
                {
                    // тут должно быть повторное оповещание на почту что фотоматериалы добвляются за неделю до последнего занятия
                    $oneErr->сritical = 1;
                    $oneErr->save();
                }
            }
            else
                $amnesty++;
        }

        if ((count($err) == 0 || count($err) == $amnesty) && $group->photos == null && date('Y-m-d', strtotime($end_time . '-14 day')) <= $now_time)
        {
            // тут ещё должно быть 1 оповещение на почту
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 3;
            $this->time_start = date("Y.m.d H:i:s");
            if (date('Y-m-d', strtotime($end_time . '-7 day')) <= $now_time)
                $this->сritical = 1;
            $this->save();
        }
    }

    public function CheckPresent($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 4])->all();
        $end_time = $group->finish_date;
        $amnesty = 0;

        foreach ($err as $oneErr)
        {
            if ($oneErr->amnesty === null)
            {
                if ($group->present_data != null)     // ошибка исправлена
                {
                    $oneErr->time_the_end = date("Y.m.d H:i:s");
                    $oneErr->save();
                }
                else if (date('Y-m-d', strtotime($end_time . '1 day')) <= $now_time)
                {
                    // прошел день последнего занятия, а инфа не добавлена? на кол!
                    $oneErr->сritical = 1;
                    $oneErr->save();
                }
            }
            else
                $amnesty++;
        }

        if ((count($err) == 0 || count($err) == $amnesty) && $group->present_data == null && $end_time <= $now_time)
        {
            // тут ещё должно быть 1 оповещение на почту
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 4;
            $this->time_start = date("Y.m.d H:i:s");
            if (date('Y-m-d', strtotime($end_time . '1 day')) <= $now_time)
                $this->сritical = 1;
            $this->save();
        }
    }

    public function CheckWork($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 5])->all();
        $end_time = $group->finish_date;
        $amnesty = 0;

        foreach ($err as $oneErr)
        {
            if ($oneErr->amnesty === null)
            {
                if ($group->work_data != null)     // ошибка исправлена
                {
                    $oneErr->time_the_end = date("Y.m.d H:i:s");
                    $oneErr->save();
                }
                else if (date('Y-m-d', strtotime($end_time . '1 day')) <= $now_time)
                {
                    // прошел день последнего занятия, а инфа не добавлена? на кол!
                    $oneErr->сritical = 1;
                    $oneErr->save();
                }
            }
            else
                $amnesty++;
        }

        if ((count($err) == 0 || count($err) == $amnesty) && $group->work_data == null && $end_time <= $now_time)
        {
            // тут ещё должно быть 1 оповещение на почту
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 5;
            $this->time_start = date("Y.m.d H:i:s");
            if (date('Y-m-d', strtotime($end_time . '1 day')) <= $now_time)
                $this->сritical = 1;
            $this->save();
        }
    }

    public function CheckCapacity($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 6])->all();
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $modelGroupID])->all();
        $lessonsCount = count($lessons);
        $capacity = TrainingProgramWork::find()->where(['id' => $group->training_program_id])->one()->capacity;
        $end_time = $group->finish_date;
        $amnesty = 0;

        foreach ($err as $oneErr)
        {
            if ($oneErr->amnesty === null)
            {
                if ($lessonsCount == $capacity)     // ошибка исправлена
                {
                    $oneErr->time_the_end = date("Y.m.d H:i:s");
                    $oneErr->save();
                }
                else if ($end_time <= $now_time)
                {
                    // на кол!
                    $oneErr->сritical = 1;
                    $oneErr->save();
                }
            }
            else
                $amnesty++;
        }

        if ((count($err) == 0 || count($err) == $amnesty) && $lessonsCount != $capacity)
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 6;
            $this->time_start = date("Y.m.d H:i:s");
            if ($end_time <= $now_time)
                $this->сritical = 1;
            $this->save();
        }
    }

    public function CheckCertificat ($modelGroupID, $group, $now_time)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 8])->all();
        $end_time = $group->finish_date;
        $certificats = TrainingGroupParticipantWork::find()->where(['training_group_id' => $modelGroupID, 'status' => 0])->all();
        $certificatCount = 0;
        $amnesty = 0;
        foreach ($certificats as $certificat)
            if ($certificat->certificat_number  === null)
                $certificatCount++;

        foreach ($err as $oneErr)
        {
            if ($oneErr->amnesty === null)
            {
                if ($certificatCount == 0)     // ошибка исправлена
                {
                    $oneErr->time_the_end = date("Y.m.d H:i:s");
                    $oneErr->save();
                }
            }
            else
                $amnesty++;
        }

        if ((count($err) == 0 || count($err) == $amnesty) && $certificatCount != 0 && $end_time <= $now_time)
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 8;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckAuditorium ($modelGroupID)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 14])->all();
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $modelGroupID])->all();
        $audsEducation = 1;
        $amnesty = 0;
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
            if ($oneErr->amnesty === null)
            {
                if ($audsEducation == 1)     // ошибка исправлена
                {
                    $oneErr->time_the_end = date("Y.m.d H:i:s");
                    $oneErr->save();
                }
            }
            else
                $amnesty++;
        }
        if ((count($err) == 0 || count($err) == $amnesty) && $audsEducation == 0)
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 14;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckErrorsTrainingGroup ($modelGroupID)
    {
        $group = TrainingGroupWork::find()->where(['id' => $modelGroupID])->one();
        $now_time = date("Y-m-d");

        $this->CheckTeacher($modelGroupID, $group, $now_time);
        $this->CheckOrder($modelGroupID, $group, $now_time);
        $this->CheckPhotos($modelGroupID, $group, $now_time);
        $this->CheckPresent($modelGroupID, $group, $now_time);
        $this->CheckWork($modelGroupID, $group, $now_time);
        $this->CheckCapacity($modelGroupID, $group, $now_time);
        $this->CheckCertificat($modelGroupID, $group, $now_time);
        $this->CheckAuditorium($modelGroupID);
    }

    public function CheckLesson ($modelGroupID)
    {
        $err = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null, 'errors_id' => 9])->all();
        $amnesty = 0;

        $now_time = date("Y-m-d");
        $finish_date = date('Y-m-d', strtotime($now_time . '-1 day'));
        $start_date = date('Y-m-d', strtotime($now_time . '-7 day'));
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $modelGroupID])->andWhere(['between', 'lesson_date', $start_date, $finish_date])->all();
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
                if ($lesson->lesson_date < strtotime($now_time . '-3 day'))
                    $checkCount = 2;
                break;
            }
        }

        foreach ($err as $oneErr)
        {
            if ($oneErr->amnesty === null) // если она не прощена стоит посмотрить исправили её или стало только хуже
            {
                if ($checkCount == 0)     // ошибка исправлена
                {
                    $oneErr->time_the_end = date("Y.m.d H:i:s");
                    $oneErr->save();
                }
                else if ($checkCount == 2)  // осталось мало времени для исправления
                {
                    $oneErr->сritical = 1;
                    $oneErr->save();
                }
            }
            else $amnesty++;
        }

        if ((count($err) == 0 || count($err) == $amnesty) &&  $checkCount != 0)
        {
            // значит кто-то детей не отмечал и на кол его посадить и письмо выслать
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 9;
            $this->time_start = $now_time;
            if ($checkCount == 2)
                $this->сritical = 1;
            $this->save();
        }
    }

    public function CheckTheme ($modelGroupID)
    {
            //LessonThemeWork::
    }

    public function CheckErrorsJournal ($modelGroupID)
    {
        $this->CheckLesson($modelGroupID);
    }

}
