<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "kind_object".
 *
 * @property int $id
 * @property string $name
 *
 * @property KindCharacteristic[] $kindCharacteristics
 * @property MaterialObject[] $materialObjects
 */
class KindObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kind_object';
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
     * Gets query for [[KindCharacteristics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKindCharacteristics()
    {
        return $this->hasMany(KindCharacteristic::className(), ['kind_object_id' => 'id']);
    }

    /**
     * Gets query for [[MaterialObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialObjects()
    {
        return $this->hasMany(MaterialObject::className(), ['kind_id' => 'id']);
    }
}
