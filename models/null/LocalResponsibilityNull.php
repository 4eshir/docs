<?php

namespace app\models\null;

use app\models\work\LocalResponsibilityWork;

class LocalResponsibilityNull extends LocalResponsibilityWork
{
    function __construct()
    {
        $this->responsibility_type_id = null;
        $this->files = null;
        $this->branch_id = null;
        $this->auditorium_id = null;
        $this->quant = null;
        $this->people_id = null;
        $this->regulation_id = null;
        $this->filesStr = null;
        $this->start_date = null;
        $this->end_date = null;
        $this->order_id = null;
    }

}