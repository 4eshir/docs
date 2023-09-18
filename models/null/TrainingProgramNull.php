<?php

namespace app\models\null;

use app\models\work\TrainingProgramWork;

class TrainingProgramNull extends TrainingProgramWork
{
    function __construct()
    {
        $this->name = null;
        $this->allow_remote_id = null;
        $this->hour_capacity = null;
        $this->capacity = null;
    }

}