<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "subobject".
 *
 * @property int $id
 * @property string $name
 * @property string|null $characteristics
 * @property int $state
 * @property int|null $parent_id
 * @property int|null $entry_id
 *
 * @property MaterialObjectSubobject[] $materialObjectSubobjects
 * @property Subobject $parent
 * @property Subobject[] $subobjects
 * @property Entry $entry
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
            [['name', 'state'], 'required'],
            [['state', 'parent_id', 'entry_id'], 'integer'],
            [['name'], 'string', 'max' => 1000],
            [['characteristics'], 'string', 'max' => 2000],
            [['parent_id'], 'exist', 'skipOnError' => true, 'targetClass' => Subobject::className(), 'targetAttribute' => ['parent_id' => 'id']],
            [['entry_id'], 'exist', 'skipOnError' => true, 'targetClass' => Entry::className(), 'targetAttribute' => ['entry_id' => 'id']],
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
            'state' => 'State',
            'parent_id' => 'Parent ID',
            'entry_id' => 'Entry ID',
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
     * Gets query for [[Entry]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEntry()
    {
        return $this->hasOne(Entry::className(), ['id' => 'entry_id']);
    }
}
