<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "responsibility_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property LocalResponsibility[] $localResponsibilities
 */
class ResponsibilityType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'responsibility_type';
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
            'name' => 'Название',
        ];
    }

    /**
     * Gets query for [[LocalResponsibilities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLocalResponsibilities()
    {
        return $this->hasMany(LocalResponsibility::className(), ['responsibility_type_id' => 'id']);
    }
}
