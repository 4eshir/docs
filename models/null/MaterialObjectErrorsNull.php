<?php

namespace app\models\null;

use app\models\work\MaterialObjectErrorsWork;

class MaterialObjectErrorsNull extends MaterialObjectErrorsWork
{
    function __construct()
    {
        $this->material_object_id = null;
        $this->errors_id = null;
        $this->time_start = null;
        $this->critical = null;
        $this->amnesty = null;
    }

}