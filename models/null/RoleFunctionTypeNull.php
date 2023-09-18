<?php

namespace app\models\null;

use app\models\work\RoleFunctionTypeWork;

class RoleFunctionTypeNull extends RoleFunctionTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}