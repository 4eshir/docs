<?php

namespace app\models\null;

use app\models\work\ContractErrorsWork;

class ContractErrorsNull extends ContractErrorsWork
{
    function __construct()
    {
        $this->contract_id = null;
        $this->errors_id = null;
        $this->time_start = null;
        $this->critical = null;
        $this->amnesty = null;
    }

}