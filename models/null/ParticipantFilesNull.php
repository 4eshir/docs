<?php

namespace app\models\null;

use app\models\work\ParticipantFilesWork;

class ParticipantFilesNull extends ParticipantFilesWork
{
    function __construct()
    {
        $this->teacher_participant_id = null;
        $this->participant_id = null;
        $this->foreign_event_id = null;
        $this->filename = null;
    }

}