<?php

namespace app\models\null;

use app\models\work\ThematicPlanWork;

class ThematicPlanNull extends ThematicPlanWork
{
    function __construct()
    {
        $this->training_program_id = null;
        $this->control_type_id = null;
        $this->theme = null;
    }

}