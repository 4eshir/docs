<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "object_entry".
 *
 * @property int $id
 * @property int $material_object_id
 * @property int $entry_id
 *
 * @property Entry $entry
 * @property MaterialObject $materialObject
 */
class ObjectEntry extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'object_entry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['entry_id'], 'required'],
            [['material_object_id', 'entry_id'], 'integer'],
            [['entry_id'], 'exist', 'skipOnError' => true, 'targetClass' => Entry::className(), 'targetAttribute' => ['entry_id' => 'id']],
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
            'entry_id' => 'Entry ID',
        ];
    }

    /**
     * Gets query for [[Entry]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntry()
    {
        return $this->hasOne(Entry::className(), ['id' => 'entry_id']);
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
