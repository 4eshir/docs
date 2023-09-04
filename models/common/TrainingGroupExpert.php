<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "training_group_expert".
 *
 * @property int $id
 * @property int $expert_id
 * @property int $training_group_id
 * @property int $expert_type_id
 *
 * @property People $expert
 * @property ExpertType $expertType
 * @property TrainingGroup $trainingGroup
 */
class TrainingGroupExpert extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training_group_expert';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['expert_id', 'training_group_id', 'expert_type_id'], 'required'],
            [['expert_id', 'training_group_id', 'expert_type_id'], 'integer'],
            [['expert_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['expert_id' => 'id']],
            [['expert_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ExpertType::className(), 'targetAttribute' => ['expert_type_id' => 'id']],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroup::className(), 'targetAttribute' => ['training_group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'expert_id' => 'Expert ID',
            'training_group_id' => 'Training Group ID',
            'expert_type_id' => 'Expert Type ID',
        ];
    }

    /**
     * Gets query for [[Expert]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpert()
    {
        return $this->hasOne(People::className(), ['id' => 'expert_id']);
    }

    /**
     * Gets query for [[ExpertType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpertType()
    {
        return $this->hasOne(ExpertType::className(), ['id' => 'expert_type_id']);
    }

    /**
     * Gets query for [[TrainingGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroup()
    {
        return $this->hasOne(TrainingGroup::className(), ['id' => 'training_group_id']);
    }
}
