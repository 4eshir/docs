<?php

namespace app\models\null;

use app\models\work\TeacherGroupWork;

class TeacherGroupNull extends TeacherGroupWork
{
    function __construct()
    {
        $this->training_group_id = null;
        $this->teacher_id = null;
    }

}