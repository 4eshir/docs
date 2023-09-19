<?php

namespace app\models\null;

use app\models\work\ExpireWork;

class ExpireNull extends ExpireWork
{
    function __construct()
    {
        $this->active_regulation_id = null;
        $this->document_type_id = null;
        $this->expire_regulation_id = null;
        $this->expire_order_id = null;
        $this->expire_type = null;
    }

}