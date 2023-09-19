<?php

namespace app\models\null;

use app\models\work\InstallPlaceWork;

class InstallPlaceNull extends InstallPlaceWork
{
    function __construct()
    {
        $this->name = null;
    }

}