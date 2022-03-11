<?php

namespace app\models\work;

use app\models\common\OrderGroupParticipant;
use app\models\work\TrainingGroupParticipantWork;
use Yii;
use yii\helpers\Html;


class OrderGroupParticipantWork extends OrderGroupParticipant
{

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

    public function getParticipantDefectors()
    {
        $trainingGroupPart = TrainingGroupParticipantWork::find();
        $trGr = TrainingGroupWork::find();

        $groupParticipant = $trainingGroupPart->where(['id' => $this->group_participant_id])->one();
        $participant = ForeignEventParticipantsWork::find()->where(['id' => $groupParticipant->participant_id])->one();
        $group = $trGr->where(['id' => $groupParticipant->training_group_id])->one();

        $defector = OrderGroupParticipantWork::find()->where(['id' => $this->link_id])->one();
        $oldGroupParticipant = $trainingGroupPart->where(['id' => $defector->group_participant_id])->one();
        $oldGroup = $trGr->where(['id' => $oldGroupParticipant->training_group_id])->one();

        $result = Html::a($participant->getFullName(), \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $participant->id]));
        $result .= " - переведен из группы ";
        $result .= Html::a($oldGroup->number, \yii\helpers\Url::to(['training-group/view', 'id' => $oldGroup->id]));
        $result .= " в группу ";
        $result .= Html::a($group->number, \yii\helpers\Url::to(['training-group/view', 'id' => $group->id]));

        return $result;
    }
}
