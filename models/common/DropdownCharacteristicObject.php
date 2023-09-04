<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "dropdown_characteristic_object".
 *
 * @property int $id
 * @property int $characteristic_object_id
 * @property string $item
 *
 * @property CharacteristicObject $characteristicObject
 */
class DropdownCharacteristicObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'dropdown_characteristic_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['characteristic_object_id', 'item'], 'required'],
            [['characteristic_object_id'], 'integer'],
            [['item'], 'string', 'max' => 1000],
            [['characteristic_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => CharacteristicObject::className(), 'targetAttribute' => ['characteristic_object_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'characteristic_object_id' => 'Characteristic Object ID',
            'item' => 'Item',
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
}
