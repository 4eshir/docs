<?php

namespace app\models\null;

use app\models\work\TrainingGroupExpertWork;

class TrainingGroupExpertNull extends TrainingGroupExpertWork
{
    function __construct()
    {
        $this->expert_id = null;
        $this->training_group_id = null;
        $this->expert_type_id = null;
    }

}