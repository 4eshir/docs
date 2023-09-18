<?php

namespace app\models\null;

use app\models\work\TrainingGroupWork;

class TrainingGroupNull extends TrainingGroupWork
{
    function __construct()
    {
        $this->number = null;
        $this->branch_id = null;
        $this->start_date = null;
        $this->finish_date = null;
        $this->budget = null;
    }

}