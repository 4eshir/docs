<?php

namespace app\models\null;

use app\models\work\AllowRemoteWork;

class AllowRemoteNull extends AllowRemoteWork
{
    function __construct()
    {
        $this->name = null;
    }

}