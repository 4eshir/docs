<?php

namespace app\models\null;

use app\models\work\OwnershipTypeWork;

class OwnershipTypeNull extends OwnershipTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}