<?php

namespace app\models\null;

use app\models\work\EventWork;

class EventNull extends EventWork
{
    function __construct()
    {
        $this->name = null;
        $this->start_date = null;
        $this->finish_date = null;
        $this->event_type_id = null;
        $this->event_form_id = null;
        $this->address = null;
        $this->event_level_id = null;
        $this->participants_count = null;
        $this->is_federal = null;
        $this->responsible_id = null;
        $this->key_words = null;
        $this->comment = null;
        $this->contains_education = null;
        $this->format = null;
        $this->responsible2_id = null;
        $this->order_id = null;
        $this->regulation_id = null;
        $this->event_way_id = null;
        $this->creator_id = null;
        $this->participation_scope_id = null;
        $this->old_name = null;
        $this->protocol = null;
        $this->photos = null;
        $this->reporting_doc = null;
        $this->other_files = null;
    }

}