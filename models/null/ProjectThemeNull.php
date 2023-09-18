<?php

namespace app\models\null;

use app\models\work\ProjectThemeWork;

class ProjectThemeNull extends ProjectThemeWork
{
    function __construct()
    {
        $this->name = null;
        $this->description = null;
    }

}