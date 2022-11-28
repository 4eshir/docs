<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "focus".
 *
 * @property int $id
 * @property string $name
 *
 * @property TrainingProgram[] $trainingPrograms
 */
class Focus extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'focus';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[TrainingPrograms]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingPrograms()
    {
        return $this->hasMany(TrainingProgram::className(), ['focus_id' => 'id']);
    }
}
