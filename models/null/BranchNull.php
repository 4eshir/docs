<?php

namespace app\models\null;

use app\models\work\BranchWork;

class BranchNull extends BranchWork
{
    function __construct()
    {
        $this->name = null;
    }

}