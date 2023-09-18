<?php

namespace app\models\null;

use app\models\work\PersonalDataWork;

class PersonalDataNull extends PersonalDataWork
{
    function __construct()
    {
        $this->name = null;
    }

}