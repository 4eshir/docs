<?php

namespace app\models\null;

use app\models\work\ThematicDirectionWork;

class ThematicDirectionNull extends ThematicDirectionWork
{
    function __construct()
    {
        $this->name = null;
        $this->full_name = null;
    }

}