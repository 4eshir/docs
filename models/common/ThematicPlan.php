<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "thematic_plan".
 *
 * @property int $id
 * @property string|null $theme
 * @property int $training_program_id
 *
 * @property TrainingProgram $trainingProgram
 */
class ThematicPlan extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'thematic_plan';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_program_id'], 'required'],
            [['training_program_id'], 'integer'],
            [['theme'], 'string', 'max' => 1000],
            [['training_program_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingProgram::className(), 'targetAttribute' => ['training_program_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'theme' => 'Наименование темы',
            'training_program_id' => 'Training Program ID',
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
}
