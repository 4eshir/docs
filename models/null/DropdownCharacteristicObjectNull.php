<?php

namespace app\models\null;

use app\models\work\DropdownCharacteristicObjectWork;

class DropdownCharacteristicObjectNull extends DropdownCharacteristicObjectWork
{
    function __construct()
    {
        $this->characteristic_object_id = null;
        $this->item = null;
    }

}