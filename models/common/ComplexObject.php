<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "complex_object".
 *
 * @property int $id
 * @property int $logical_union_id
 * @property int $material_object_id
 *
 * @property Complex $logicalUnion
 * @property MaterialObject $materialObject
 */
class ComplexObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'complex_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['logical_union_id', 'material_object_id'], 'required'],
            [['logical_union_id', 'material_object_id'], 'integer'],
            [['logical_union_id'], 'exist', 'skipOnError' => true, 'targetClass' => Complex::className(), 'targetAttribute' => ['logical_union_id' => 'id']],
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
            'logical_union_id' => 'Logical Union ID',
            'material_object_id' => 'Material Object ID',
        ];
    }

    /**
     * Gets query for [[LogicalUnion]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLogicalUnion()
    {
        return $this->hasOne(Complex::className(), ['id' => 'logical_union_id']);
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
