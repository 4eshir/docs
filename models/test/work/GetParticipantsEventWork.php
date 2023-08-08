<?php

namespace app\models\test\work;

use app\models\test\common\GetParticipantsEvent;

class GetParticipantsEventWork extends GetParticipantsEvent
{
    public function __construct($t_name = null, $t_event_type_id = null, $t_event_form_id = null, $t_event_level_id = null, $t_finish_date = null)
    {
        if ($t_name === null)
            parent::__construct();
        else
        {
            $this->name = $t_name;
            $this->event_type_id = $t_event_type_id;
            $this->event_form_id = $t_event_form_id;
            $this->event_level_id = $t_event_level_id;
            $this->finish_date = $t_finish_date;
        }
    }
}