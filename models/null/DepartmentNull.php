<?php

namespace app\models\null;

use app\models\work\DepartmentWork;

class DepartmentNull extends DepartmentWork
{
    function __construct()
    {
        $this->name = null;
    }

}