<?php

namespace app\models\null;

use app\models\work\EventTypeWork;

class EventTypeNull extends EventTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}