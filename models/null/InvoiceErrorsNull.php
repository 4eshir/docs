<?php

namespace app\models\null;

use app\models\work\InvoiceErrorsWork;

class InvoiceErrorsNull extends InvoiceErrorsWork
{
    function __construct()
    {
        $this->invoice_id = null;
        $this->errors_id = null;
        $this->time_start = null;
        $this->critical = null;
        $this->amnesty = null;
    }

}