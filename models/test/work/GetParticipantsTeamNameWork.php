<?php

namespace app\models\test\work;

use app\models\test\common\GetParticipantsTeamName;

class GetParticipantsTeamNameWork extends GetParticipantsTeamName
{
    public function __construct($t_name = null, $t_foreign_event_id = null)
    {
        if ($t_name === null)
            parent::__construct();
        else
        {
            $this->name = $t_name;
            $this->foreign_event_id = $t_foreign_event_id;
        }
    }


}