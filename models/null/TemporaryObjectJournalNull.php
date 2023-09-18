<?php

namespace app\models\null;

use app\models\work\TemporaryObjectJournalWork;

class TemporaryObjectJournalNull extends TemporaryObjectJournalWork
{
    function __construct()
    {
        $this->user_give_id = null;
        $this->user_get_id = null;
        $this->material_object_id = null;
        $this->date_give = null;
        $this->date_get = null;
    }

}