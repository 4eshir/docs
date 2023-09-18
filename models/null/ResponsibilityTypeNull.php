<?php

namespace app\models\null;

use app\models\work\ResponsibilityTypeWork;

class ResponsibilityTypeNull extends ResponsibilityTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}