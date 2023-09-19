<?php

namespace app\models\null;

use app\models\work\EventParticipantsWork;

class EventParticipantsNull extends EventParticipantsWork
{
    function __construct()
    {
        $this->child_participants = null;
        $this->child_rst_participants = null;
        $this->other_participants = null;
        $this->teacher_participants = null;
        $this->age_left_border = null;
        $this->age_right_border = null;
        $this->event_id = null;
    }

}