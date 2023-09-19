<?php

namespace app\models\null;

use app\models\work\LicenseWork;

class LicenseNull extends LicenseWork
{
    function __construct()
    {
        $this->name = null;
    }

}