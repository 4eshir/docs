<?php

namespace app\models\null;

use app\models\work\EventLevelWork;

class EventLevelNull extends EventLevelWork
{
    function __construct()
    {
        $this->name = null;
    }

}