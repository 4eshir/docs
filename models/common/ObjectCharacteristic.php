<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "object_characteristic".
 *
 * @property int $id
 * @property int $material_object_id
 * @property int $characteristic_object_id
 * @property int|null $integer_value
 * @property float|null $double_value
 * @property string|null $string_value
 * @property int|null $bool_value
<<<<<<< HEAD
 * @property string $date_value
=======
 * @property string|null $date_value
>>>>>>> afd4af68d2f6bd11cbe6fec6ade082a579c4df5e
 *
 * @property CharacteristicObject $characteristicObject
 * @property MaterialObject $materialObject
 */
class ObjectCharacteristic extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'object_characteristic';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
<<<<<<< HEAD
            [['material_object_id', 'characteristic_object_id', 'date_value'], 'required'],
=======
            [['material_object_id', 'characteristic_object_id'], 'required'],
>>>>>>> afd4af68d2f6bd11cbe6fec6ade082a579c4df5e
            [['material_object_id', 'characteristic_object_id', 'integer_value', 'bool_value'], 'integer'],
            [['double_value'], 'number'],
            [['date_value'], 'safe'],
            [['string_value'], 'string', 'max' => 1000],
            [['characteristic_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => CharacteristicObject::className(), 'targetAttribute' => ['characteristic_object_id' => 'id']],
            [['material_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObject::className(), 'targetAttribute' => ['material_object_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'material_object_id' => 'Material Object ID',
            'characteristic_object_id' => 'Characteristic Object ID',
            'integer_value' => 'Integer Value',
            'double_value' => 'Double Value',
            'string_value' => 'String Value',
            'bool_value' => 'Bool Value',
            'date_value' => 'Date Value',
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
     * Gets query for [[MaterialObject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialObject()
    {
        return $this->hasOne(MaterialObject::className(), ['id' => 'material_object_id']);
    }
}
