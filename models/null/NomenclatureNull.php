<?php

namespace app\models\null;

use app\models\work\NomenclatureWork;

class NomenclatureNull extends NomenclatureWork
{
    function __construct()
    {
        $this->branch_id = null;
        $this->actuality = null;
        $this->number = null;
        $this->name = null;
    }

}