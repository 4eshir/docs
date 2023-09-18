<?php

namespace app\models\null;

use app\models\work\ProgramErrorsWork;

class ProgramErrorsNull extends ProgramErrorsWork
{
    function __construct()
    {
        $this->training_program_id = null;
        $this->errors_id = null;
        $this->time_start = null;
        $this->critical = null;
        $this->amnesty = null;
    }

}