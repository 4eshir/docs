<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "access".
 *
 * @property int $id
 * @property string $name
 *
 * @property AccessLevel[] $accessLevels
 */
class Access extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access';
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
     * Gets query for [[AccessLevels]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAccessLevels()
    {
        return $this->hasMany(AccessLevel::className(), ['access_id' => 'id']);
    }
}
