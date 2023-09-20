<?php

namespace app\models\null;

use app\models\work\CompanyTypeWork;

class CompanyTypeNull extends CompanyTypeWork
{
    function __construct()
    {
        $this->type = null;
    }

}