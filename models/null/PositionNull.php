<?php

namespace app\models\null;

use app\models\work\PositionWork;

class PositionNull extends PositionWork
{
    function __construct()
    {
        $this->name = null;
    }

}