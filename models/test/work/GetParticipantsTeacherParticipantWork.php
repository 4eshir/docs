<?php

namespace app\models\test\work;

use app\models\test\common\GetParticipantsTeacherParticipant;

class GetParticipantsTeacherParticipantWork extends GetParticipantsTeacherParticipant
{
    public function __construct($t_participant_id = null, $t_teacher_id = null, $t_teacher2_id = null, $t_foreign_event_id = null, $t_focus = null, $t_allow_remote_id = null)
    {
        if ($t_participant_id === null)
            parent::__construct();
        else
        {
            $this->participant_id = $t_participant_id;
            $this->teacher_id = $t_teacher_id;
            $this->teacher2_id = $t_teacher2_id;
            $this->foreign_event_id = $t_foreign_event_id;
            $this->focus = $t_focus;
            $this->allow_remote_id = $t_allow_remote_id;
        }
    }
}