<?php

namespace app\models\work;

use app\models\null\ExpertTypeNull;
use app\models\null\PeopleNull;
use Yii;
use app\models\common\TrainingGroupExpert;
use app\models\common\People;
use app\models\common\ExpertType;
use app\models\common\TrainingGroup;


class TrainingGroupExpertWork extends TrainingGroupExpert
{
	public function rules()
    {
        return [
            [['expert_id', 'training_group_id', 'expert_type_id'], 'integer'],
            [['expert_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['expert_id' => 'id']],
            [['expert_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExpertType::className(), 'targetAttribute' => ['expert_type_id' => 'id']],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroup::className(), 'targetAttribute' => ['training_group_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'expert_id' => 'ФИО эксперта',
            'expert_type_id' => 'Внутренний эксперт',
        ];
    }

    public function getExpertWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'expert_id']);
        return $try->all() ? $try : new PeopleNull();
    }

    public function getExpertTypeWork()
    {
        $try = $this->hasOne(ExpertTypeWork::className(), ['id' => 'expert_type_id']);
        return $try->all() ? $try : new ExpertTypeNull();
    }

}
