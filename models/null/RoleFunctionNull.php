<?php

namespace app\models\null;

use app\models\work\RoleFunctionWork;

class RoleFunctionNull extends RoleFunctionWork
{
    function __construct()
    {
        $this->name = null;
        $this->role_function_type_id = null;
    }

}