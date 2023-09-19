<?php

namespace app\models\null;

use app\models\work\HistoryObjectWork;

class HistoryObjectNull extends HistoryObjectWork
{
    function __construct()
    {
        $this->material_object_id = null;
        $this->count = null;
        $this->history_transaction_id = null;
        $this->container_id = null;
    }

}