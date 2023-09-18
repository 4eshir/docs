<?php

namespace app\models\null;

use app\models\work\TeacherParticipantWork;

class TeacherParticipantNull extends TeacherParticipantWork
{
    function __construct()
    {
        $this->participant_id = null;
        $this->teacher_id = null;
        $this->teacher2_id = null;
        $this->foreign_event_id = null;
        $this->focus = null;
        $this->allow_remote_id = null;
    }

}