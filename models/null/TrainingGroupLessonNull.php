<?php

namespace app\models\null;

use app\models\work\TrainingGroupLessonWork;

class TrainingGroupLessonNull extends TrainingGroupLessonWork
{
    function __construct()
    {
        $this->lesson_date = null;
        $this->lesson_start_time = null;
        $this->lesson_end_time = null;
        $this->duration = null;
        $this->training_group_id = null;
    }

}