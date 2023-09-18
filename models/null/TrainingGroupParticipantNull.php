<?php

namespace app\models\null;

use app\models\work\TrainingGroupParticipantWork;

class TrainingGroupParticipantNull extends TrainingGroupParticipantWork
{
    function __construct()
    {
        $this->training_group_id = null;
        $this->participant_id = null;
        $this->certificat_number = null;
        $this->send_method_id = null;
        $this->status = null;
        $this->success = null;
        $this->points = null;
        $this->group_project_themes_id = null;
    }

}