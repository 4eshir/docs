<?php

namespace app\models\work;

use app\models\common\LessonTheme;
use app\models\work\PeopleWork;
use app\models\work\TeacherGroupWork;
use Yii;


class LessonThemeWork extends LessonTheme
{
    public function getTeacherWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'teacher_id']);
    }

    public function getTrainingGroupWork()
    {
        return $this->hasOne(TrainingGroupWork::className(), ['id' => 'training_group_id']);
    }
}
