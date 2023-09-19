<?php

namespace app\models\null;

use app\models\work\LicenseTermTypeWork;

class LicenseTermTypeNull extends LicenseTermTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}