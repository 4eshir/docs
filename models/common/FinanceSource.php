<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "finance_source".
 *
 * @property int $id
 * @property string $name
 *
 * @property MaterialObject[] $materialObjects
 */
class FinanceSource extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'finance_source';
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
     * Gets query for [[MaterialObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialObjects()
    {
        return $this->hasMany(MaterialObject::className(), ['finance_source_id' => 'id']);
    }
}
