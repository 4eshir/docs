<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "program_errors".
 *
 * @property int $id
 * @property int $training_program_id
 * @property int $errors_id
 * @property string $time_start
 * @property string|null $time_the_end
 * @property int|null $critical
 * @property int|null $amnesty
 *
 * @property TrainingProgram $trainingProgram
 * @property Errors $errors0
 */
class ProgramErrors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_errors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_program_id', 'errors_id', 'time_start'], 'required'],
            [['training_program_id', 'errors_id', 'critical', 'amnesty'], 'integer'],
            [['time_start', 'time_the_end'], 'safe'],
            [['training_program_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingProgram::className(), 'targetAttribute' => ['training_program_id' => 'id']],
            [['errors_id'], 'exist', 'skipOnError' => true, 'targetClass' => Errors::className(), 'targetAttribute' => ['errors_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'training_program_id' => 'Training Program ID',
            'errors_id' => 'Errors ID',
            'time_start' => 'Time Start',
            'time_the_end' => 'Time The End',
            'critical' => 'Critical',
            'amnesty' => 'Amnesty',
        ];
    }

    /**
     * Gets query for [[TrainingProgram]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingProgram()
    {
        return $this->hasOne(TrainingProgram::className(), ['id' => 'training_program_id']);
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

    public function getCritical()
    {
        return $this->critical;
    }
}
