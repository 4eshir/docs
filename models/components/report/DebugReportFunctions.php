<?php

namespace app\models\components\report;

use app\models\components\report\debug_models\DebugManHoursModel;
use app\models\work\LessonThemeWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use app\models\work\VisitWork;

class DebugReportFunctions
{
    //--Функция, возвращающая дополнительные данные для отчета по обучающимся (человеко-часам)--
    /*
     * $groups - массив класса TrainingGroupWork, группы для учета
     * [$start_date : $end_date] - Промежуток для поиска занятий и явок (границы включены)
     * $status - массив допустимых к выгрузке
     */
    static public function DebugDataManHours($groups, $start_date, $end_date, $status, $teachers = [])
    {
        $modelsArr = [];

        foreach ($groups as $group)
        {
            $model = new DebugManHoursModel();
            $model->group = $group->number;

            $lessonAllTemp = TrainingGroupLessonWork::find()->where(['training_group_id' => $group->id])
                ->andWhere(['>=', 'lesson_date', $start_date])
                ->andWhere(['<=', 'lesson_date', $end_date])->all();

            if ($teachers !== [])
            {
                $lessonThemes = LessonThemeWork::find()->where(['IN', 'teacher_id', $teachers])->all();
                $ltIds = [];
                foreach ($lessonThemes as $one) $ltIds[] = $one->training_group_lesson_id;

                $lessonTeacherTemp = TrainingGroupLessonWork::find()->where(['IN', 'id', $ltIds])->all();
            }
                else
                    $lessonTeacherTemp = $lessonAllTemp;

            $lttIds = SupportReportFunctions::GetIdFromArray($lessonTeacherTemp);

            $participantsTemp = TrainingGroupParticipantWork::find()->where(['training_group_id' => $group->id])->all();
            $visitsTemp = VisitWork::find()->where(['IN', 'training_group_lesson_id', $lttIds])
                ->andWhere(['IN', 'status', $status])->all();

            $model->lessonsAll = $lessonAllTemp;
            $model->lessonsChangeTeacher = $lessonTeacherTemp;
            $model->participants = $participantsTemp;
            $model->manHours = $visitsTemp;

            $modelsArr[] = $model;
        }

        return $modelsArr;
    }
    //------------------------------------------------------------------------------------------


    //--Функция, возвращающая дополнительные данные для отчета по обучающимся (кол-во обучающихся)--
    /*
     * $participants - массив обучающихся (полный класс TrainingGroupParticipantWork или только participant->id
     * $unic - признак уникальности (0 - все обучающиеся, 1 - только уникальные)
     * $groupsId - идентификаторы учебных групп (для доп. поиска, если выбран unic = 1)
     */
    static public function DebugDataParticipantsCount($section, $participants, $unic, $groupsId = [])
    {
        $result = '';



        if ($unic == 0)
        {
            foreach ($participants as $participant)
                $result .= $participant->participantWork->fullName.";".
                           $participant->trainingGroupWork->number.";".
                           $participant->trainingGroupWork->start_date.";".
                           $participant->trainingGroupWork->finish_date.";".
                           $participant->trainingGroupWork->branchWork->name.";".
                           $participant->participantWork->sex.";".
                           $participant->participantWork->birthdate.";".
                           $participant->trainingGroupWork->trainingProgramWork->focusWork->name.";".
                           $participant->trainingGroupWork->teachersArray[0]->teacherWork->shortName.";".
                           $participant->trainingGroupWork->budgetText.";".
                           $participant->trainingGroupWork->trainingProgramWork->thematicDirectionWork->full_name.";".
                           $participant->trainingGroupWork->trainingProgramWork->name.";".
                           $participant->groupProjectThemesWork->projectThemeWork->name.";".
                           $participant->trainingGroupWork->protection_date.";".
                           $participant->groupProjectThemesWork->projectTypeWork->name.";".
                           $participant->trainingGroupWork->expertsArray[0]->expertWork->fullName.";".
                           $participant->trainingGroupWork->expertsArray[0]->expertWork->companyWork->name.";".
                           $participant->trainingGroupWork->expertsArray[0]->expertTypeWork->name.";".
                           $participant->trainingGroupWork->expertsArray[0]->expertWork->positionWork->name.";".
                           $section."\r\n";

        }
        else
        {
            // если обучающиеся уникальные, то у нас есть только $participant->participant_id
            $participantsId = [];
            foreach ($participants as $participant) $participantsId[] = $participant->participant_id;

            var_dump('ffffff');

            foreach ($participantsId as $pId)
            {
                $participant = TrainingGroupParticipantWork::find()->where(['participant_id' => $pId])->andWhere(['IN', 'training_group_id', $groupsId])->one();
                $result .= $participant->participantWork->fullName.";".
                    $participant->trainingGroupWork->number.";".
                    $participant->trainingGroupWork->start_date.";".
                    $participant->trainingGroupWork->finish_date.";".
                    $participant->trainingGroupWork->branchWork->name.";".
                    $participant->participantWork->sex.";".
                    $participant->participantWork->birthdate.";".
                    $participant->trainingGroupWork->trainingProgramWork->focusWork->name.";".
                    $participant->trainingGroupWork->teachersArray[0]->teacherWork->shortName.";".
                    $participant->trainingGroupWork->budgetText.";".
                    $participant->trainingGroupWork->trainingProgramWork->thematicDirectionWork->full_name.";".
                    $participant->trainingGroupWork->trainingProgramWork->name.";".
                    $participant->groupProjectThemesWork->projectThemeWork->name.";".
                    $participant->trainingGroupWork->protection_date.";".
                    $participant->groupProjectThemesWork->projectTypeWork->name.";".
                    $participant->trainingGroupWork->expertsArray[0]->expertWork->fullName.";".
                    $participant->trainingGroupWork->expertsArray[0]->expertWork->companyWork->name.";".
                    $participant->trainingGroupWork->expertsArray[0]->expertTypeWork->name.";".
                    $participant->trainingGroupWork->expertsArray[0]->expertWork->positionWork->name.";".
                    $section."\r\n";
            }

        }

        return $result;
    }
    //----------------------------------------------------------------------------------------------
}