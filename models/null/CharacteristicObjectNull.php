<?php

namespace app\models\null;

use app\models\work\CharacteristicObjectWork;

class CharacteristicObjectNull extends CharacteristicObjectWork
{
    function __construct()
    {
        $this->name = null;
        $this->value_type = null;
    }

}