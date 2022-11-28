<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "material_object_subobject".
 *
 * @property int $id
 * @property int $material_object_id
 * @property int $subobject_id
 *
 * @property MaterialObject $materialObject
 * @property Subobject $subobject
 */
class MaterialObjectSubobject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'material_object_subobject';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['material_object_id', 'subobject_id'], 'required'],
            [['material_object_id', 'subobject_id'], 'integer'],
            [['material_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObject::className(), 'targetAttribute' => ['material_object_id' => 'id']],
            [['subobject_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subobject::className(), 'targetAttribute' => ['subobject_id' => 'id']],
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
            'subobject_id' => 'Subobject ID',
        ];
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

    /**
     * Gets query for [[Subobject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubobject()
    {
        return $this->hasOne(Subobject::className(), ['id' => 'subobject_id']);
    }
}
