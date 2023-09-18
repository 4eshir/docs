<?php

namespace app\models\null;

use app\models\work\TrainingGroupObjectWork;

class TrainingGroupObjectNull extends TrainingGroupObjectWork
{
    function __construct()
    {
        $this->material_object_id = null;
        $this->training_group_id = null;
    }

}