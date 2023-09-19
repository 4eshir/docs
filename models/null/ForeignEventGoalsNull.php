<?php

namespace app\models\null;

use app\models\work\ForeignEventGoalsWork;

class ForeignEventGoalsNull extends ForeignEventGoalsWork
{
    function __construct()
    {
        $this->name = null;
    }

}