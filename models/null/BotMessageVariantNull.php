<?php

namespace app\models\null;

use app\models\work\BotMessageVariantWork;

class BotMessageVariantNull extends BotMessageVariantWork
{
    function __construct()
    {
        $this->bot_message_id = null;
        $this->text = null;
        $this->picture = null;
        $this->next_bot_message_id = null;
    }

}