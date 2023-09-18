<?php

namespace app\models\null;

use app\models\work\PeopleMaterialObjectWork;

class PeopleMaterialObjectNull extends PeopleMaterialObjectWork
{
    function __construct()
    {
        $this->people_id = null;
        $this->material_object_id = null;
        $this->acceptance_date = null;
    }

}