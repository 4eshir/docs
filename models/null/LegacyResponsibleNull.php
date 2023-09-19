<?php

namespace app\models\null;

use app\models\work\LegacyResponsibleWork;

class LegacyResponsibleNull extends LegacyResponsibleWork
{
    function __construct()
    {
        $this->responsibility_type_id = null;
        $this->quant = null;
        $this->auditorium_id = null;
        $this->branch_id = null;
        $this->people_id = null;
        $this->start_date = null;
        $this->end_date = null;
        $this->order_id = null;
    }

}