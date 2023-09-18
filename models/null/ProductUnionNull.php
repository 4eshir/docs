<?php

namespace app\models\null;

use app\models\work\ProductUnionWork;

class ProductUnionNull extends ProductUnionWork
{
    function __construct()
    {
        $this->name = null;
        $this->count = null;
        $this->average_price = null;
        $this->average_cost = null;
        $this->date = null;
    }

}