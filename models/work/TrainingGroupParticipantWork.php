<?php

namespace app\models\work;

use app\models\common\TrainingGroupParticipant;
use Yii;


class TrainingGroupParticipantWork extends TrainingGroupParticipant
{
    public function getParticipantWork()
    {
        return $this->hasOne(ForeignEventParticipantsWork::className(), ['id' => 'participant_id']);
    }

    public function getTrainingGroupWork()
    {
        return $this->hasOne(TrainingGroupWork::className(), ['id' => 'training_group_id']);
    }
}
