<?php

namespace app\models\null;

use app\models\work\RoleFunctionRoleWork;

class RoleFunctionRoleNull extends RoleFunctionRoleWork
{
    function __construct()
    {
        $this->role_function_id = null;
        $this->role_id = null;
    }

}