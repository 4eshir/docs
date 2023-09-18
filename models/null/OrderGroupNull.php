<?php

namespace app\models\null;

use app\models\work\OrderGroupWork;

class OrderGroupNull extends OrderGroupWork
{
    function __construct()
    {
        $this->document_order_id = null;
        $this->training_group_id = null;
        $this->comment = null;
    }

}