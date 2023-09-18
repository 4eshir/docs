<?php

namespace app\models\null;

use app\models\work\TemporaryJournalWork;

class TemporaryJournalNull extends TemporaryJournalWork
{
    function __construct()
    {
        $this->material_object_id = null;
        $this->give_people_id = null;
        $this->gain_people_id = null;
        $this->date_issue = null;
        $this->approximate_time = null;
        $this->branch_id = null;
        $this->event_id = null;
        $this->foreign_event_id = null;
    }

}