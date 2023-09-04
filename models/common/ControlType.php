<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "control_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property ThematicPlan[] $thematicPlans
 */
class ControlType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'control_type';
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
     * Gets query for [[ThematicPlans]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getThematicPlans()
    {
        return $this->hasMany(ThematicPlan::className(), ['control_type_id' => 'id']);
    }
}
