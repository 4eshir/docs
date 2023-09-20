<?php

namespace app\models\null;

use app\models\work\ControlTypeWork;

class ControlTypeNull extends ControlTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}