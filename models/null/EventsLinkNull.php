<?php

namespace app\models\null;

use app\models\work\EventsLinkWork;

class EventsLinkNull extends EventsLinkWork
{
    function __construct()
    {
        $this->event_external_id = null;
        $this->event_id = null;
        $this->eventExternalName = null;
    }

}