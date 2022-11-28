<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "history_transaction".
 *
 * @property int $id
 * @property int $user_get_id получивший
 * @property int $user_give_id отдавший
 * @property string $date когда произошла передача объекта
 *
 * @property User $userGet
 * @property User $userGive
 */
class HistoryTransaction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'history_transaction';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_get_id', 'user_give_id', 'date'], 'required'],
            [['user_get_id', 'user_give_id'], 'integer'],
            [['date'], 'safe'],
            [['user_get_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_get_id' => 'id']],
            [['user_give_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_give_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_get_id' => 'User Get ID',
            'user_give_id' => 'User Give ID',
            'date' => 'Date',
        ];
    }

    /**
     * Gets query for [[UserGet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserGet()
    {
        return $this->hasOne(User::className(), ['id' => 'user_get_id']);
    }

    /**
     * Gets query for [[UserGive]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserGive()
    {
        return $this->hasOne(User::className(), ['id' => 'user_give_id']);
    }
}
