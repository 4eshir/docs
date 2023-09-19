<?php

namespace app\models\null;

use app\models\work\DistributionTypeWork;

class DistributionTypeNull extends DistributionTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}