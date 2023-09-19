<?php

namespace app\models\null;

use app\models\work\DestinationWork;

class DestinationNull extends DestinationWork
{
    function __construct()
    {
        $this->company_id = null;
        $this->position_id = null;
    }

}