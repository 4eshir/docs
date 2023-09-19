<?php

namespace app\models\null;

use app\models\work\InvoiceEntryWork;

class InvoiceEntryNull extends InvoiceEntryWork
{
    function __construct()
    {
        $this->invoice_id = null;
        $this->entry_id = null;
    }

}