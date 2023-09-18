<?php

namespace app\models\null;

use app\models\work\ResponsibleWork;

class ResponsibleNull extends ResponsibleWork
{
    function __construct()
    {
        $this->fio = null;
        $this->people_id = null;
        $this->document_order_id = null;
    }

}