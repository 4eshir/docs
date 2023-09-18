<?php

namespace app\models\null;

use app\models\work\TeamWork;

class TeamNull extends TeamWork
{
    function __construct()
    {
        $this->teacher_participant_id = null;
        $this->foreign_event_id = null;
        $this->participant_id = null;
        $this->team_name_id = null;
        $this->name = null;
    }

}