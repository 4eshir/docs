<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "expire".
 *
 * @property int $id
 * @property int $active_regulation_id
 * @property int $expire_regulation_id
 *
 * @property Regulation $activeRegulation
 * @property Regulation $expireRegulation
 */
class Expire extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'expire';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['active_regulation_id', 'expire_regulation_id'], 'required'],
            [['active_regulation_id', 'expire_regulation_id'], 'integer'],
            [['active_regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regulation::className(), 'targetAttribute' => ['active_regulation_id' => 'id']],
            [['expire_regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regulation::className(), 'targetAttribute' => ['expire_regulation_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'active_regulation_id' => 'Active Regulation ID',
            'expire_regulation_id' => 'Expire Regulation ID',
        ];
    }

    /**
     * Gets query for [[ActiveRegulation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActiveRegulation()
    {
        return $this->hasOne(Regulation::className(), ['id' => 'active_regulation_id']);
    }

    /**
     * Gets query for [[ExpireRegulation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpireRegulation()
    {
        return $this->hasOne(Regulation::className(), ['id' => 'expire_regulation_id']);
    }
}
