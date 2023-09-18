<?php

namespace app\models\null;

use app\models\work\ParticipationScopeWork;

class ParticipationScopeNull extends ParticipationScopeWork
{
    function __construct()
    {
        $this->name = null;
    }

}