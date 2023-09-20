<?php

namespace app\models\null;

use app\models\work\CountryWork;

class CountryNull extends CountryWork
{
    function __construct()
    {
        $this->name = null;
    }

}