<?php

namespace app\models\work;

use app\models\common\TrainingGroupParticipant;
use Yii;

/**
 * This is the model class for table "training_group_participant".
 *
 * @property int $id
 * @property int $participant_id
 * @property string|null $certificat_number
 * @property int|null $send_method_id
 * @property int $training_group_id
 * @property int $status
 *
 * @property ForeignEventParticipants $participant
 * @property SendMethod $sendMethod
 * @property TrainingGroup $trainingGroup
 */
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
