<?php

namespace app\models\null;

use app\models\work\EventBranchWork;

class EventBranchNull extends EventBranchWork
{
    function __construct()
    {
        $this->event_id = null;
        $this->branch_id = null;
    }

}