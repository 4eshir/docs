<?php

namespace app\models\test\work;

use app\models\test\common\GetParticipantAchievementsParticipantAchievement;

class GetParticipantAchievementsParticipantAchievementWork extends GetParticipantAchievementsParticipantAchievement
{
    public function __construct($t_teacherParticipantId = null, $t_winner = null)
    {
        if ($t_teacherParticipantId == null)
            parent::__construct();
        else
        {
            $this->teacher_participant_id = $t_teacherParticipantId;
            $this->winner = $t_winner;
        }

    }

}