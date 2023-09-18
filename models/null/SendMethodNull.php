<?php

namespace app\models\null;

use app\models\work\SendMethodWork;

class SendMethodNull extends SendMethodWork
{
    function __construct()
    {
        $this->name = null;
    }

}