<?php

namespace app\models\null;

use app\models\work\ContainerErrorsWork;

class ContainerErrorsNull extends ContainerErrorsWork
{
    function __construct()
    {
        $this->container_id = null;
        $this->errors_id = null;
        $this->time_start = null;
        $this->critical = null;
        $this->amnesty = null;
    }

}