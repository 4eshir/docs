<?php

namespace app\models\null;

use app\models\work\LicenseTypeWork;

class LicenseTypeNull extends LicenseTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}