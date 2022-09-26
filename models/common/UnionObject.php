<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "union_object".
 *
 * @property int $id
 * @property int $material_object_id
 * @property int $complex_id
 *
 * @property ProductUnion $complex
 * @property MaterialObject $materialObject
 */
class UnionObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'union_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['material_object_id', 'complex_id'], 'required'],
            [['material_object_id', 'complex_id'], 'integer'],
            [['complex_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductUnion::className(), 'targetAttribute' => ['complex_id' => 'id']],
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
            'complex_id' => 'Complex ID',
        ];
    }

    /**
     * Gets query for [[Complex]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComplex()
    {
        return $this->hasOne(ProductUnion::className(), ['id' => 'complex_id']);
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
