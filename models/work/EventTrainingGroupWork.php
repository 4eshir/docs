<?php

namespace app\models\work;

use app\models\common\EventTrainingGroup;
use app\models\null\TrainingGroupNull;
use Yii;


class EventTrainingGroupWork extends EventTrainingGroup
{
	public function rules()
    {
        return [
            [['event_id', 'training_group_id'], 'integer'],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventWork::className(), 'targetAttribute' => ['event_id' => 'id']],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupWork::className(), 'targetAttribute' => ['training_group_id' => 'id']],
        ];
    }

    public function getTrainingGroupWork()
    {
        $try = $this->hasOne(TrainingGroupWork::className(), ['id' => 'training_group_id']);
        return $try->all() ? $try : new TrainingGroupNull();
    }
}
