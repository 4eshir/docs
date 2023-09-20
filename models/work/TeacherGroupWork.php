<?php

namespace app\models\work;

use app\models\common\TeacherGroup;
use app\models\null\PeopleNull;
use app\models\null\TrainingGroupNull;
use app\models\work\PeopleWork;
use Yii;


class TeacherGroupWork extends TeacherGroup
{
    public function getTeacherWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'teacher_id']);
        return $try->all() ? $try : new PeopleNull();
    }

    public function getTrainingGroupWork()
    {
        $try = $this->hasOne(TrainingGroupWork::className(), ['id' => 'training_group_id']);
        return $try->all() ? $try : new TrainingGroupNull();
    }
}
