<?php

namespace app\models\null;

use app\models\work\AuditoriumTypeWork;

class AuditoriumTypeNull extends AuditoriumTypeWork
{
    function __construct()
    {
        $this->name = null;
    }

}