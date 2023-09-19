<?php

namespace app\models\null;

use app\models\work\LegacyMaterialResponsibilityWork;

class LegacyMaterialResponsibilityNull extends LegacyMaterialResponsibilityWork
{
    function __construct()
    {
        $this->people_out_id = null;
        $this->people_in_id = null;
        $this->material_object_id = null;
        $this->date = null;
    }

}