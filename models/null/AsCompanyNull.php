<?php

namespace app\models\null;

use app\models\work\AsCompanyWork;

class AsCompanyNull extends AsCompanyWork
{
    function __construct()
    {
        $this->name = null;
    }

}