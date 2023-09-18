<?php

namespace app\models\null;

use app\models\work\LogWork;

class LogNull extends LogWork
{
    function __construct()
    {
        $this->user_id = null;
        $this->text = null;
        $this->date = null;
        $this->time = null;
    }

}