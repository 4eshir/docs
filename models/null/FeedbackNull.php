<?php

namespace app\models\null;

use app\models\work\FeedbackWork;

class FeedbackNull extends FeedbackWork
{
    function __construct()
    {
        $this->user_id = null;
        $this->text = null;
        $this->answer = null;
    }

}