<?php

namespace app\models\null;

use app\models\work\AccessWork;

class AccessNull extends AccessWork
{
    function __construct()
    {
        $this->name = null;
    }

}