<?php

namespace app\models\null;

use app\models\work\PersonalDataForeignEventParticipantWork;

class PersonalDataForeignEventParticipantNull extends PersonalDataForeignEventParticipantWork
{
    function __construct()
    {
        $this->foreign_event_participant_id = null;
        $this->personal_data_id = null;
        $this->status = null;
    }

}