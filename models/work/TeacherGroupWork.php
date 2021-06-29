<?php

namespace app\models\work;

use app\models\common\TeacherGroup;
use app\models\work\PeopleWork;
use Yii;


class TeacherGroupWork extends TeacherGroup
{
    public function getTeacherWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'teacher_id']);
    }
}
