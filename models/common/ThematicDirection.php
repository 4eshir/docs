<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "thematic_direction".
 *
 * @property int $id
 * @property string $name
 * @property string $full_name
 *
 * @property TrainingProgram[] $trainingPrograms
 */
class ThematicDirection extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'thematic_direction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'full_name'], 'required'],
            [['name', 'full_name'], 'string', 'max' => 1000],
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
        return $this->hasMany(TrainingProgram::className(), ['thematic_direction_id' => 'id']);
    }

}
