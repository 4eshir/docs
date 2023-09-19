<?php

namespace app\models\null;

use app\models\work\EventScopeWork;

class EventScopeNull extends EventScopeWork
{
    function __construct()
    {
        $this->event_id = null;
        $this->participation_scope_id = null;
    }

}