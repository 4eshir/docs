<?php

namespace app\models\null;

use app\models\work\CategorySmspWork;

class CategorySmspNull extends CategorySmspWork
{
    function __construct()
    {
        $this->name = null;
    }

}