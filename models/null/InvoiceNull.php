<?php

namespace app\models\null;

use app\models\work\InvoiceWork;

class InvoiceNull extends InvoiceWork
{
    function __construct()
    {
        $this->number = null;
        $this->contractor_id = null;
        $this->date_product = null;
        $this->date_invoice = null;
        $this->type = null;
        $this->document = null;
    }

}