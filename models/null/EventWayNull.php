<?php

namespace app\models\null;

use app\models\work\EventWayWork;

class EventWayNull extends EventWayWork
{
    function __construct()
    {
        $this->name = null;
    }

}