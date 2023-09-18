<?php

namespace app\models\null;

use app\models\work\PeoplePositionBranchWork;

class PeoplePositionBranchNull extends PeoplePositionBranchWork
{
    function __construct()
    {
        $this->people_id = null;
        $this->position_id = null;
        $this->branch_id = null;
    }

}