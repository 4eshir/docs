<?php

namespace app\models\null;

use app\models\work\ExpertTypeWork;

class ExpertTypeNull extends ExpertTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}