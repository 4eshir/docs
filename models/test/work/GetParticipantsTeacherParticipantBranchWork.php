<?php

namespace app\models\test\work;

use app\models\test\common\GetParticipantsTeacherParticipantBranch;

class GetParticipantsTeacherParticipantBranchWork extends GetParticipantsTeacherParticipantBranch
{
    public function __construct($t_branch_id = null, $t_teacher_participant_id = null)
    {
        if ($t_branch_id === null)
            parent::__construct();
        else
        {
            $this->branch_id = $t_branch_id;
            $this->teacher_participant_id = $t_teacher_participant_id;
        }
    }

    public function getTeacherParticipantWork()
    {
        return $this->hasOne(GetParticipantsTeacherParticipantWork::className(), ['id' => 'teacher_participant_id']);
    }
}