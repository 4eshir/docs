<?php

namespace app\models\null;

use app\models\work\CategoryContractWork;

class CategoryContractNull extends CategoryContractWork
{
    function __construct()
    {
        $this->name = null;
    }

}