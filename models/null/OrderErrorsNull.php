<?php

namespace app\models\null;

use app\models\work\OrderErrorsWork;

class OrderErrorsNull extends OrderErrorsWork
{
    function __construct()
    {
        $this->document_order_id = null;
        $this->errors_id = null;
        $this->time_start = null;
        $this->critical = null;
        $this->amnesty = null;
    }

}