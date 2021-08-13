<?php

namespace app\models\work;

use app\models\common\GroupErrors;
use app\models\work\ErrorsWork;
use Yii;


class GroupErrorsWork extends GroupErrors
{
    public function CheckErrorsTrainingGroup ($modelGroupID)
    {
        $oldErrors = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null])->all();

        $teacherCount = count(TeacherGroupWork::find()->where(['training_group_id' => $modelGroupID])->all());
        $group = TrainingGroupWork::find()->where(['id' => $modelGroupID])->one();
        $ordersCount = count(OrderGroupWork::find()->where(['training_group_id' => $modelGroupID])->all());
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $modelGroupID])->all();
        $lessonsCount = count($lessons);
        $capacity = TrainingProgramWork::find()->where(['id' => $group->training_program_id])->one()->capacity;
        $certificats = TrainingGroupParticipantWork::find()->where(['training_group_id' => $modelGroupID, 'status' => 0])->all();

        $certificatCount = 0;
        foreach ($certificats as $certificat)
            if ($certificat->certificat_number  === null)
                $certificatCount++;

        $audsEducation = 1;
        foreach ($lessons as $lesson) {
            $audsLessons = $lesson->auditorium_id;
            $auditorium = AuditoriumWork::find()->where(['id' => $audsLessons])->one();
            if ($auditorium->is_education == 0)
            {
                $audsEducation = 0;
                break;
            }
        }

        $start_time = $group->start_date;
        $end_time = $group->finish_date;
        $now_time = date("Y-m-d");

        $checkList = ['teacher' => 0, 'order' => 0, 'photos' => 0, 'present' => 0, 'work' => 0, 'capacity' => 0, 'certificat' => 0, 'auds' => 0];

        // если ошибки есть - проверяем исправили ли их
        foreach ($oldErrors as $correctErrors)
        {
            if ($correctErrors->errors_id == 1)
            {
                $checkList['teacher'] = 1;
                if ($teacherCount != 0)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
                else if ($start_time <= $now_time)
                {
                    // в первого день занятия ещё нет препода? на кол!
                }
            }

            if ($correctErrors->errors_id == 2)
            {
                $checkList['order'] = 1;
                if ($ordersCount != 0)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
                else if ($end_time <= $now_time)
                {
                    // тут должно быть повторное оповещание на почту что приказ должен быть добавлен в день последнего занятия
                }
            }

            if ($correctErrors->errors_id == 3)
            {
                $checkList['photos'] = 1;
                if ($group->photos != null)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
                else if (date('Y-m-d', strtotime($end_time . '-7 day')) <= $now_time)
                {
                    // тут должно быть повторное оповещание на почту что фотоматериалы добвляются за неделю до последнего занятия
                }
            }

            if ($correctErrors->errors_id == 4)
            {
                $checkList['present'] = 1;
                if ($group->present_data != null)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
                else if (date('Y-m-d', strtotime($end_time . '1 day')) <= $now_time)
                {
                    // прошел день последнего занятия, а инфа не добавлена? на кол!
                }
            }

            if ($correctErrors->errors_id == 5)
            {
                $checkList['work'] = 1;
                if ($group->work_data != null)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
                else if (date('Y-m-d', strtotime($end_time . '1 day')) <= $now_time)
                {
                    // прошел день последнего занятия, а инфа не добавлена? на кол!
                }
            }

            if ($correctErrors->errors_id == 6)
            {
                $checkList['capacity'] = 1;
                if ($lessonsCount == $capacity)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
                else if ($end_time <= $now_time)
                {
                    // на кол!
                }
            }

            if ($correctErrors->errors_id == 8)
            {
                $checkList['certificat'] = 1;
                if ($certificatCount == 0)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
            }

            if ($correctErrors->errors_id == 14)
            {
                $checkList['auds'] = 1;
                if ($audsEducation == 1)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
            }

            $correctErrors->save();
        }

        // проверяем новые косяки и не смотрим то, что уже было просмотрено

        if ($checkList['teacher'] == 0 && $teacherCount == 0)
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 1;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }

        if ($checkList['order'] == 0 && $ordersCount == 0 && $start_time <= $now_time)
        {
            // тут ещё должно быть 1 оповещение на почту
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 2;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }

        if ($checkList['photos'] == 0 && $group->photos == null && date('Y-m-d', strtotime($end_time . '-14 day')) <= $now_time)
        {
            // тут ещё должно быть 1 оповещение на почту
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 3;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }

        if ($checkList['present'] == 0 && $group->present_data == null && $end_time <= $now_time)
        {
            // тут ещё должно быть 1 оповещение на почту
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 4;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }

        if ($checkList['work'] == 0 && $group->work_data == null && $end_time <= $now_time)
        {
            // тут ещё должно быть 1 оповещение на почту
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 5;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }

        if ($checkList['capacity'] == 0 && $lessonsCount != $capacity)
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 6;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }

        if ($checkList['certificat'] == 0 && $certificatCount != 0 && $end_time <= $now_time)
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 8;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }

        if ($checkList['auds'] == 0 && $audsEducation == 0)
        {
            $this->training_group_id = $modelGroupID;
            $this->errors_id = 14;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckErrorsTrainingProgram ($modelProgramID)
    {
        //$oldErrors = GroupErrorsWork::find()->where(['training_group_id' => $modelGroupID, 'time_the_end' => null])->all();

        $program = TrainingProgramWork::find()->where(['id' => $modelProgramID])->one();
        $tp = ThematicPlanWork::find()->where(['training_program_id' => $modelProgramID])->all();
        $tpCount = count($tp);
        $controle = 0;
        $authorsCount = count(AuthorProgramWork::find()->where(['training_program_id' => $modelProgramID])->all());

        foreach ($tp as $plane)
        {
            if ($plane->control_type_id === null)
                $controle++;
        }

        $checkList = ['tematicPlane' => 0, 'capacity' => 0, 'controle' => 0, 'thematicDirection' => 0, 'authors' => 0];

        /*foreach ($oldErrors as $correctErrors)
        {
            if ($correctErrors->errors_id == 7)
            {
                $checkList['tematicPlane'] = 1;
                if ($tpCount > 0)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
            }

            if ($correctErrors->errors_id == 10)
            {
                $checkList['thematicDirection'] = 1;
                if ($program->thematic_direction_id !== null)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
            }

            if ($correctErrors->errors_id == 11)
            {
                $checkList['controle'] = 1;
                if ($controle == 0)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
            }

            if ($correctErrors->errors_id == 12)
            {
                $checkList['capacity'] = 1;
                if ($tpCount == $program->capacity)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
            }

            if ($correctErrors->errors_id == 13)
            {
                $checkList['authors'] = 1;
                if ($authorsCount > 0)     // ошибка исправлена
                    $correctErrors->time_the_end = date("Y.m.d H:i:s");
            }

            $correctErrors->save();
        }*/

        if ($checkList['tematicPlane'] == 0 && $tpCount == 0) // не заполнено утп
        {
            //$this->training_group_id = $modelGroupID;
            $this->errors_id = 7;
            $this->time_start = date("Y.m.d H:i:s");
            //$this->save();
        }
        else if ($checkList['capacity'] == 0 && $tpCount !== $program->capacity)
        {
            // объем программы и утп не совпадают количество часов
            //$this->training_group_id = $modelGroupID;
            $this->errors_id = 12;
            $this->time_start = date("Y.m.d H:i:s");
            //$this->save();
        }

        // в утп не указана форма контроля 11
        if ($checkList['controle'] == 0 && $controle > 0)
        {
            //$this->training_group_id = $modelGroupID;
            $this->errors_id = 11;
            $this->time_start = date("Y.m.d H:i:s");
            //$this->save();
        }

        // не заполнено тематическое напрвление
        if ($checkList['thematicDirection'] == 0 && $program->thematic_direction_id === null)
        {
            //$this->training_group_id = $modelGroupID;
            $this->errors_id = 10;
            $this->time_start = date("Y.m.d H:i:s");
            //$this->save();
        }

        // не указаны составители 13
        if ($checkList['authors'] == 0 && $authorsCount == 0)
        {
            //$this->training_group_id = $modelGroupID;
            $this->errors_id = 13;
            $this->time_start = date("Y.m.d H:i:s");
            //$this->save();
        }

    }
}
