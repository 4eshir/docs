<?php

namespace app\models\null;

use app\models\work\ForeignEventWork;

class ForeignEventNull extends ForeignEventWork
{
    function __construct()
    {
        $this->name = null;
        $this->company_id = null;
        $this->start_date = null;
        $this->finish_date = null;
        $this->event_way_id = null;
        $this->event_level_id = null;
        $this->min_participants_age = null;
        $this->max_participants_age = null;
        $this->business_trip = null;
        $this->key_words = null;
        $this->city = null;
        $this->escort_id = null;
        $this->order_participation_id = null;
        $this->order_business_trip_id = null;
        $this->docs_achievement = null;
        $this->copy = null;
        $this->creator_id = null;
        $this->is_minpros = null;
        $this->last_edit_id = null;
        $this->participants = null;
        $this->achievement = null;
        $this->achievement = null;
        $this->team = null;
    }

}