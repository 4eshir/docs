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
}