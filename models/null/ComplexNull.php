<?php

namespace app\models\null;

use app\models\work\ComplexWork;

class ComplexNull extends ComplexWork
{
    function __construct()
    {
        $this->name = null;
    }

}