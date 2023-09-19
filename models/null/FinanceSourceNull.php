<?php

namespace app\models\null;

use app\models\work\FinanceSourceWork;

class FinanceSourceNull extends FinanceSourceWork
{
    function __construct()
    {
        $this->name = null;
    }

}