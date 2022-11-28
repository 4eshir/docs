<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "characteristic_object".
 *
 * @property int $id
 * @property string $name
 * @property int $value_type 1 - целое, 2 - дробное, 3 - строковое
 *
 * @property KindCharacteristic[] $kindCharacteristics
 * @property ObjectCharacteristic[] $objectCharacteristics
 */
class CharacteristicObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'characteristic_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'value_type'], 'required'],
            [['value_type'], 'integer'],
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
            'value_type' => 'Value Type',
        ];
    }

    /**
     * Gets query for [[KindCharacteristics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKindCharacteristics()
    {
        return $this->hasMany(KindCharacteristic::className(), ['characteristic_object_id' => 'id']);
    }

    /**
     * Gets query for [[ObjectCharacteristics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjectCharacteristics()
    {
        return $this->hasMany(ObjectCharacteristic::className(), ['characteristic_object_id' => 'id']);
    }
}
