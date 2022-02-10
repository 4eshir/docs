<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "group_errors".
 *
 * @property int $id
 * @property int $training_group_id
 * @property int $errors_id
 * @property string $time_start
 * @property string|null $time_the_end
 * @property int|null $critical
 * @property int|null $amnesty
 *
 * @property Errors $errors0
 * @property TrainingGroup $trainingGroup
 */
class GroupErrors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'group_errors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_group_id', 'errors_id', 'time_start'], 'required'],
            [['training_group_id', 'errors_id', 'critical', 'amnesty'], 'integer'],
            [['time_start', 'time_the_end'], 'safe'],
            [['errors_id'], 'exist', 'skipOnError' => true, 'targetClass' => Errors::className(), 'targetAttribute' => ['errors_id' => 'id']],
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
            'training_group_id' => 'Training Group ID',
            'errors_id' => 'Errors ID',
            'time_start' => 'Time Start',
            'time_the_end' => 'Time The End',
            'critical' => 'Critical',
            'amnesty' => 'Amnesty',
        ];
    }

    /**
     * Gets query for [[Errors0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getErrors0()
    {
        return $this->hasOne(Errors::className(), ['id' => 'errors_id']);
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

    /**
     * Gets query for [[critical]].
     *
     * @return int|null
     */
    public function getCritical()
    {
        return $this->critical;
    }
}
