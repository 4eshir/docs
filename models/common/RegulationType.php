<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "regulation_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property Regulation[] $regulations
 */
class RegulationType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'regulation_type';
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
     * Gets query for [[Regulations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegulations()
    {
        return $this->hasMany(Regulation::className(), ['regulation_type_id' => 'id']);
    }
}
