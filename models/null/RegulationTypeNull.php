<?php

namespace app\models\null;

use app\models\work\RegulationTypeWork;

class RegulationTypeNull extends RegulationTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}