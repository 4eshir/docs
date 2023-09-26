<?php

namespace app\models\components\report;

use app\models\components\report\debug_models\DebugManHoursModel;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use app\models\work\VisitWork;

class DebugReportFunctions
{
    //--Функция, возвращающая дополнительные данные для отчета по обучающимся (человеко-часам)--
    /*
     * $visits - массив класса visit (посещения по заданным параметрам)
     * [$start_date : $end_date] - Промежуток для поиска занятий и явок (границы включены)
     */
    static public function DebugDataManHours($visits, $start_date, $end_date)
    {
        $visits = VisitWork::find()->where(['IN', 'id', $visits])->all();

        $modelsArr = [new DebugManHoursModel];

        $participantsId = [];
        $lessonsId = [];
        foreach ($visits as $visit)
        {
            $lessonsId[] = $visit->training_group_lesson_id;
            $participantsId[] = $visit->foreign_event_participant_id;
        }

        $lessons = TrainingGroupLessonWork::find()->where(['IN', 'id', $lessonsId])->all();

        $groupsId = [];
        foreach ($lessons as $lesson) $groupsId[] = $lesson->training_group_id;

        $groups = TrainingGroupWork::find()->where(['IN', 'id', $groupsId])->all();

        foreach ($groups as $group)
        {
            $model = new DebugManHoursModel();
            $model->group = $group->number;

            $lessonAllTemp = TrainingGroupLessonWork::find()->where(['training_group_id' => $group->id])->all();
            $lessonTeacherTemp = TrainingGroupLessonWork::find()->where(['training_group_id' => $group->id])->andWhere(['IN', 'id', $lessonsId])->all();
            $participantsTemp = TrainingGroupParticipantWork::find()->where(['training_group_id' => $group->id])->all();
            $visitsTemp = VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])
                ->where(['trainingGroupLesson.training_group_id' => $group->id])
                ->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])
                ->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])->all();

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