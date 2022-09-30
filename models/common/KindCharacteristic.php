<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "kind_characteristic".
 *
 * @property int $id
 * @property int $kind_object_id
 * @property int $characteristic_object_id
 *
 * @property CharacteristicObject $characteristicObject
 * @property KindObject $kindObject
 */
class KindCharacteristic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'kind_characteristic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['kind_object_id', 'characteristic_object_id'], 'required'],
            [['kind_object_id', 'characteristic_object_id'], 'integer'],
            [['characteristic_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => CharacteristicObject::className(), 'targetAttribute' => ['characteristic_object_id' => 'id']],
            [['kind_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => KindObject::className(), 'targetAttribute' => ['kind_object_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'kind_object_id' => 'Kind Object ID',
            'characteristic_object_id' => 'Characteristic Object ID',
        ];
    }

    /**
     * Gets query for [[CharacteristicObject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCharacteristicObject()
    {
        return $this->hasOne(CharacteristicObject::className(), ['id' => 'characteristic_object_id']);
    }

    /**
     * Gets query for [[KindObject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKindObject()
    {
        return $this->hasOne(KindObject::className(), ['id' => 'kind_object_id']);
    }
}
