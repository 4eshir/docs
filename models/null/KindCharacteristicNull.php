<?php

namespace app\models\null;

use app\models\work\KindCharacteristicWork;

class KindCharacteristicNull extends KindCharacteristicWork
{
    function __construct()
    {
        $this->kind_object_id = null;
        $this->characteristic_object_id = null;
    }

}