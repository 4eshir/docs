<?php

namespace app\models\null;

use app\models\work\CertificatTypeWork;

class CertificatTypeNull extends CertificatTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}