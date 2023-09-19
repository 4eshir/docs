<?php

namespace app\models\null;

use app\models\work\EventFormWork;

class EventFormNull extends EventFormWork
{
    function __construct()
    {
        $this->name = null;
    }

}