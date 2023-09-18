<?php

namespace app\models\null;

use app\models\work\VisitWork;

class VisitNull extends VisitWork
{
    function __construct()
    {
        $this->foreign_event_participant_id = null;
        $this->training_group_lesson_id = null;
    }

}