<?php

namespace app\models\null;

use app\models\work\ForeignEventParticipantsWork;

class ForeignEventParticipantsNull extends ForeignEventParticipantsWork
{
    function __construct()
    {
        $this->firstname = null;
        $this->secondname = null;
        $this->sex = null;
        $this->email = null;
        $this->is_true = null;
        $this->guaranted_true = null;
        $this->pd = null;
        $this->birthdate = null;
    }

}