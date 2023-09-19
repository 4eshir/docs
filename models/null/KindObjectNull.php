<?php

namespace app\models\null;

use app\models\work\KindObjectWork;

class KindObjectNull extends KindObjectWork
{
    function __construct()
    {
        $this->name = null;
    }

}