<?php

namespace app\models\work;

use app\models\common\LessonTheme;
use app\models\common\People;
use Yii;


class LessonThemeWork extends LessonTheme
{
    public function getTeacher()
    {
        return $this->hasOne(People::className(), ['id' => 'teacher_id']);
    }
}
