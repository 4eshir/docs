<?php

namespace app\models\null;

use app\models\work\RoleWork;

class RoleNull extends RoleWork
{
    function __construct()
    {
        $this->name = null;
        $this->functions = null;
        $this->role_id = null;
    }

}