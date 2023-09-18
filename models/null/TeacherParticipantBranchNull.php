<?php

namespace app\models\null;

use app\models\work\TeacherParticipantBranchWork;

class TeacherParticipantBranchNull extends TeacherParticipantBranchWork
{
    function __construct()
    {
        $this->branch_id = null;
        $this->teacher_participant_id = null;
    }

}