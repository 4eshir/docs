<?php

namespace app\models\null;

use app\models\work\ErrorsWork;

class ErrorsNull extends ErrorsWork
{
    function __construct()
    {
        $this->name = null;
        $this->number = null;
    }

}