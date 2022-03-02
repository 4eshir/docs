<?php

namespace app\models\work;

use app\models\common\OrderGroupParticipant;
use Yii;
use yii\helpers\Html;


class OrderGroupParticipantWork extends OrderGroupParticipant
{
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_group_id' => 'Order Group ID',
            'group_participant_id' => 'Group Participant ID',
            'status' => 'Status',
        ];
    }

    public function getParticipantAndGroup()
    {
        $groupParticipant = TrainingGroupParticipantWork::find()->where(['id' => $this->group_participant_id])->one();
        $participant = ForeignEventParticipantsWork::find()->where(['id' => $groupParticipant->participant_id])->one();
        $group = TrainingGroupWork::find()->where(['id' => $groupParticipant->training_group_id])->one();
        $result = Html::a($participant->getFullName(), \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $participant->id]));
        $result .= " - учащийся группы ";
        $result .= Html::a($group->number, \yii\helpers\Url::to(['training-group/view', 'id' => $group->id]));
        return $result;
    }
}
