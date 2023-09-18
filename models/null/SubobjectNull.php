<?php

namespace app\models\null;

use app\models\work\SubobjectWork;

class SubobjectNull extends SubobjectWork
{
    function __construct()
    {
        $this->name = null;
        $this->state = null;
        $this->parent_id = null;
        $this->entry_id = null;
        $this->characteristics = null;
    }

}