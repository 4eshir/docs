<?php

namespace app\models\null;

use app\models\work\ParticipantForeignEventWork;

class ParticipantForeignEventNull extends ParticipantForeignEventWork
{
    function __construct()
    {
        $this->participant_id = null;
        $this->foreign_event_id = null;
    }

}