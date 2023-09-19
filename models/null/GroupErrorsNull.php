<?php

namespace app\models\null;

use app\models\work\GroupErrorsWork;

class GroupErrorsNull extends GroupErrorsWork
{
    function __construct()
    {
        $this->training_group_id = null;
        $this->errors_id = null;
        $this->time_start = null;
        $this->critical = null;
        $this->amnesty = null;
    }

}