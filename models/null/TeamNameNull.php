<?php

namespace app\models\null;

use app\models\work\TeamNameWork;

class TeamNameNull extends TeamNameWork
{
    function __construct()
    {
        $this->foreign_event_id = null;
        $this->name = null;
    }

}