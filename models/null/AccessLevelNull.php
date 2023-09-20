<?php

namespace app\models\null;

use app\models\work\AccessLevelWork;

class AccessLevelNull extends AccessLevelWork
{
    function __construct()
    {
        $this->user_id = null;
        $this->role_function_id = null;
        $this->start_time = null;
        $this->end_time = null;
        $this->weeks = null;
        $this->days = null;
        $this->hours = null;
    }

}