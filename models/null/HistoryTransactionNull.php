<?php

namespace app\models\null;

use app\models\work\HistoryTransactionWork;

class HistoryTransactionNull extends HistoryTransactionWork
{
    function __construct()
    {
        $this->people_get_id = null;
        $this->date = null;
        $this->people_give_id = null;
    }

}