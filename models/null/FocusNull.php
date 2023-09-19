<?php

namespace app\models\null;

use app\models\work\FocusWork;

class FocusNull extends FocusWork
{
    function __construct()
    {
        $this->name = null;
    }

}