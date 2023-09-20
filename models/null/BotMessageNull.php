<?php

namespace app\models\null;

use app\models\work\BotMessageWork;

class BotMessageNull extends BotMessageWork
{
    function __construct()
    {
        $this->text = null;
    }

}