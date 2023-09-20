<?php

namespace app\models\null;

use app\models\work\ComplexObjectWork;

class ComplexObjectNull extends ComplexObjectWork
{
    function __construct()
    {
        $this->logical_union_id = null;
        $this->material_object_id = null;
    }

}