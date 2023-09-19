<?php

namespace app\models\null;

use app\models\work\ForeignEventErrorsWork;

class ForeignEventErrorsNull extends ForeignEventErrorsWork
{
    function __construct()
    {
        $this->foreign_event_id = null;
        $this->errors_id = null;
        $this->time_start = null;
        $this->critical = null;
        $this->amnesty = null;
    }

}