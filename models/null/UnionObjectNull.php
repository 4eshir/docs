<?php

namespace app\models\null;

use app\models\work\UnionObjectWork;

class UnionObjectNull extends UnionObjectWork
{
    function __construct()
    {
        $this->material_object_id = null;
        $this->union_id = null;
    }

}