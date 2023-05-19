<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "union_object".
 *
 * @property int $id
 * @property int $material_object_id
 * @property int $union_id
 *
 * @property MaterialObject $materialObject
 * @property ProductUnion $union
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
            [['material_object_id', 'union_id'], 'required'],
            [['material_object_id', 'union_id'], 'integer'],
            [['material_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObject::className(), 'targetAttribute' => ['material_object_id' => 'id']],
            [['union_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProductUnion::className(), 'targetAttribute' => ['union_id' => 'id']],
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
            'union_id' => 'Union ID',
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
     * Gets query for [[Union]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnion()
    {
        return $this->hasOne(ProductUnion::className(), ['id' => 'union_id']);
    }
}
