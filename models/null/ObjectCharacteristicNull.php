<?php

namespace app\models\null;

use app\models\work\ObjectCharacteristicWork;

class ObjectCharacteristicNull extends ObjectCharacteristicWork
{
    function __construct()
    {
        $this->material_object_id = null;
        $this->characteristic_object_id = null;
        $this->integer_value = null;
        $this->bool_value = null;
        $this->dropdown_value = null;
        $this->double_value = null;
        $this->date_value = null;
        $this->string_value = null;
        $this->document_value = null;
    }

}