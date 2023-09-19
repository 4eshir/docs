<?php

namespace app\models\null;

use app\models\work\ProjectTypeWork;

class ProjectTypeNull extends ProjectTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}