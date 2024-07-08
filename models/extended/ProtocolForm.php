<?php

namespace app\models\extended;

use app\models\work\TrainingGroupParticipantWork;
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
        $this->participants = TrainingGroupParticipantWork::find()->joinWith('participant participant')->where(['training_group_id' => $groupId])->all();
    }
}