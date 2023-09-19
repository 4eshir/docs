<?php

namespace app\models\null;

use app\models\work\LessonThemeWork;

class LessonThemeNull extends LessonThemeWork
{
    function __construct()
    {
        $this->theme = null;
        $this->training_group_lesson_id = null;
        $this->teacher_id = null;
        $this->control_type_id = null;
    }

}