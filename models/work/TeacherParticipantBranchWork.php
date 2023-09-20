<?php

namespace app\models\work;

use app\models\common\TeacherParticipantBranch;
use app\models\null\TeacherParticipantNull;
use app\models\null\BranchNull;
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
        $try = $this->hasOne(TeacherParticipantWork::className(), ['id' => 'teacher_participant_id']);
        return $try->all() ? $try : new TeacherParticipantNull();
    }

    public function getBranchWork()
    {
        $try = $this->hasOne(BranchWork::className(), ['id' => 'branch_id']);
        return $try->all() ? $try : new BranchNull();
    }
}
