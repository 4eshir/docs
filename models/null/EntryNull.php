<?php

namespace app\models\null;

use app\models\work\EntryWork;

class EntryNull extends EntryWork
{
    function __construct()
    {
        $this->name = null;
        $this->create_date = null;
        $this->lifetime = null;
        $this->expirationDate = null;
        $this->amount = null;
        $this->complex = null;
        $this->price = null;
        $this->inventory_number = null;
        $this->dynamic = null;
        $this->characteristics = null;
        $this->attribute = null;
        $this->kind_id = null;
        $this->object_id = null;
    }

}