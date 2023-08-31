<?php

namespace app\models\work;

use app\models\common\TeacherParticipantBranch;
use Yii;


class TeacherParticipantBranchWork extends TeacherParticipantBranch
{
    public $teacherParticipantWork;
    function __construct($tId = null, $tBranchId = null, $tTeacherParticipantId = null, $tParticipantId = null)
    {
        if ($tId === null)
            return;

        $this->id = $tId;
        $this->branch_id = $tBranchId;
        $this->teacher_participant_id = $tTeacherParticipantId;

        $this->teacherParticipantWork = new TeacherParticipantWork($tTeacherParticipantId, $tParticipantId, null, null, null, null, null);
    }

    public function getTeacherParticipantWork()
    {
        return $this->hasOne(TeacherParticipantWork::className(), ['id' => 'teacher_participant_id']);
    }

    public function getBranchWork()
    {
        return $this->hasOne(BranchWork::className(), ['id' => 'branch_id']);
    }
}
