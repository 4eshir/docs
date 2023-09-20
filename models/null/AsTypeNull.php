<?php

namespace app\models\null;

use app\models\work\AsTypeWork;

class AsTypeNull extends AsTypeWork
{
    function __construct()
    {
        $this->type = null;
    }

}