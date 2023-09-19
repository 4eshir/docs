<?php

namespace app\models\null;

use app\models\work\EventObjectWork;

class EventObjectNull extends EventObjectWork
{
    function __construct()
    {
        $this->event_id = null;
        $this->material_object_id = null;
    }

}