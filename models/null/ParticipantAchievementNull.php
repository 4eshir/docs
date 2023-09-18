<?php

namespace app\models\null;

use app\models\work\ParticipantAchievementWork;

class ParticipantAchievementNull extends ParticipantAchievementWork
{
    function __construct()
    {
        $this->teacher_participant_id = null;
        $this->achievment = null;
        $this->participant_id = null;
        $this->foreign_event_id = null;
        $this->winner = null;
        $this->date = null;
        $this->cert_number = null;
        $this->nomination = null;
    }

}