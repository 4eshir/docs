<?php

namespace app\models\null;

use app\models\work\CertificatTemplatesWork;

class CertificatTemplatesNull extends CertificatTemplatesWork
{
    function __construct()
    {
        $this->name = null;
        $this->path = null;
    }

}