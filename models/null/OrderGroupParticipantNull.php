<?php

namespace app\models\null;

use app\models\work\OrderGroupParticipantWork;

class OrderGroupParticipantNull extends OrderGroupParticipantWork
{
    function __construct()
    {
        $this->order_group_id = null;
        $this->group_participant_id = null;
        $this->status = null;
        $this->link_id = null;
    }

}