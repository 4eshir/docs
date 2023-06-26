<?php

namespace app\models\work;

use app\models\common\TeacherParticipantBranch;
use Yii;


class TeacherParticipantBranchWork extends TeacherParticipantBranch
{
	public function getTeacherParticipantWork()
    {
        return $this->hasOne(TeacherParticipantWork::className(), ['id' => 'teacher_participant_id']);
    }

    public function getBranchWork()
    {
        return $this->hasOne(BranchWork::className(), ['id' => 'branch_id']);
    }
}
