<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "people_material_object".
 *
 * @property int $id
 * @property int $people_id
 * @property int $material_object_id
 *
 * @property MaterialObject $materialObject
 * @property People $people
 */
class PeopleMaterialObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'people_material_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['people_id', 'material_object_id'], 'required'],
            [['people_id', 'material_object_id'], 'integer'],
            [['material_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObject::className(), 'targetAttribute' => ['material_object_id' => 'id']],
            [['people_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['people_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'people_id' => 'People ID',
            'material_object_id' => 'Material Object ID',
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
     * Gets query for [[People]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasOne(People::className(), ['id' => 'people_id']);
    }
}
