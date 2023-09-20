<?php

namespace app\models\null;

use app\models\work\BranchProgramWork;

class BranchProgramNull extends BranchProgramWork
{
    function __construct()
    {
        $this->branch_id = null;
        $this->training_program_id = null;
    }

}