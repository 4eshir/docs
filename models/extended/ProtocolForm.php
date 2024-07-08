<?php

namespace app\models\extended;

use app\models\work\OrderGroupParticipantWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use yii\base\Model;

class ProtocolForm extends Model
{
    public $dropdownEventName;
    public $textEventName;
    public $participants;
    public $chooseParticipants;

    public function rules()
    {
        return [
            [['textEventName', 'chooseParticipants'], 'required'],
            [['dropdownEventName', 'textEventName', 'participants', 'chooseParticipants'], 'safe'],
        ];
    }

    public function __construct($groupId, $config = [])
    {
        parent::__construct($config);
        $group = TrainingGroupWork::find()->where(['id' => $groupId])->one();
        $expelledParticipant = OrderGroupParticipantWork::find()
            ->select('order_group_participant.group_participant_id')
            ->joinWith(['orderGroup'])
            ->joinWith(['orderGroup.documentOrder'])
            ->where(['order_group.training_group_id' => $groupId])
            ->andWhere(['in', 'order_group_participant.status', [1, 2]])
            ->andWhere(['<', 'document_order.order_date', $group->protection_date])
            ->asArray()
            ->all();
        $arrExpelledParticipant = array_column($expelledParticipant, 'group_participant_id');

        $participantInProtocol = array_filter($group->trainingGroupParticipantsWork, function($participant) use ($arrExpelledParticipant) {
            return !in_array($participant->id, $arrExpelledParticipant);
        });
        //$this->participants = TrainingGroupParticipantWork::find()->joinWith('participant participant')->where(['training_group_id' => $groupId])->all();
        $this->participants = $participantInProtocol;
    }
}