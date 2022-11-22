<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "subobject".
 *
 * @property int $id
 * @property string $name
 * @property string|null $characteristics
 * @property int|null $parent_id
 * @property int|null $material_object_id
 *
 * @property MaterialObjectSubobject[] $materialObjectSubobjects
 * @property Subobject $parent
 * @property Subobject[] $subobjects
 * @property MaterialObject $materialObject
 */
class Subobject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'subobject';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['parent_id', 'material_object_id'], 'integer'],
            [['name'], 'string', 'max' => 1000],
            [['characteristics'], 'string', 'max' => 2000],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subobject::className(), 'targetAttribute' => ['parent_id' => 'id']],
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
            'name' => 'Name',
            'characteristics' => 'Characteristics',
            'parent_id' => 'Parent ID',
            'material_object_id' => 'Material Object ID',
        ];
    }

    /**
     * Gets query for [[MaterialObjectSubobjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialObjectSubobjects()
    {
        return $this->hasMany(MaterialObjectSubobject::className(), ['subobject_id' => 'id']);
    }

    /**
     * Gets query for [[Parent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParent()
    {
        return $this->hasOne(Subobject::className(), ['id' => 'parent_id']);
    }

    /**
     * Gets query for [[Subobjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSubobjects()
    {
        return $this->hasMany(Subobject::className(), ['parent_id' => 'id']);
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
