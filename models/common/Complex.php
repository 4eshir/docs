<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "complex".
 *
 * @property int $id
 * @property string $name
 *
 * @property ComplexObject[] $complexObjects
 */
class Complex extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'complex';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 1000],
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
        ];
    }

    /**
     * Gets query for [[ComplexObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComplexObjects()
    {
        return $this->hasMany(ComplexObject::className(), ['logical_union_id' => 'id']);
    }
}
