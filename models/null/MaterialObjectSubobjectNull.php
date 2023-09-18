<?php

namespace app\models\null;

use app\models\work\MaterialObjectSubobjectWork;

class MaterialObjectSubobjectNull extends MaterialObjectSubobjectWork
{
    function __construct()
    {
        $this->material_object_id = null;
        $this->subobject_id = null;
    }

}