<?php

namespace app\models\null;

use app\models\work\EventTrainingGroupWork;

class EventTrainingGroupNull extends EventTrainingGroupWork
{
    function __construct()
    {
        $this->event_id = null;
        $this->training_group_id = null;
    }

}