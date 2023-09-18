<?php

namespace app\models\null;

use app\models\work\RegulationWork;

class RegulationNull extends RegulationWork
{
    function __construct()
    {
        $this->date = null;
        $this->name = null;
        $this->order_id = null;
        $this->state = null;
    }

}