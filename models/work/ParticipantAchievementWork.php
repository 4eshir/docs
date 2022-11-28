<?php

namespace app\models\work;

use app\models\common\ParticipantAchievement;
use Yii;


class ParticipantAchievementWork extends ParticipantAchievement
{
    public function getParticipantWork()
    {
        return $this->hasOne(ForeignEventParticipantsWork::className(), ['id' => 'participant_id']);
    }
}
