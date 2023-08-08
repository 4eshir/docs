<?php

namespace app\models\test\work;

use app\models\test\common\GetParticipantsTeam;

class GetParticipantsTeamWork extends GetParticipantsTeam
{
    public function __construct($t_name = null, $t_teacher_participant_id = null)
    {
        if ($t_name === null)
            parent::__construct();
        else
        {
            $this->name = $t_name;
            $this->teacher_participant_id = $t_teacher_participant_id;
        }
    }
}