<?php

namespace app\models\null;

use app\models\work\ContractWork;

class ContractNull extends ContractWork
{
    function __construct()
    {
        $this->date = null;
        $this->number = null;
        $this->file = null;
        $this->key_words = null;
        $this->contractor_id = null;
    }

}