<?php

namespace app\models\null;

use app\models\work\MaterialObjectTypeWork;

class MaterialObjectTypeNull extends MaterialObjectTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}