<?php

namespace app\models\null;

use app\models\work\EventExternalWork;

class EventExternalNull extends EventExternalWork
{
    function __construct()
    {
        $this->name = null;
    }

}