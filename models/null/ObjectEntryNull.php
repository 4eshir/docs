<?php

namespace app\models\null;

use app\models\work\ObjectEntryWork;

class ObjectEntryNull extends ObjectEntryWork
{
    function __construct()
    {
        $this->entry_id = null;
        $this->material_object_id = null;
    }

}