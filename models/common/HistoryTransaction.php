<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "history_transaction".
 *
 * @property int $id
 * @property int $user_get_id получивший
 * @property int|null $user_give_id отдавший
 * @property string $date когда произошла передача объекта
 *
 * @property HistoryObject[] $historyObjects
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
            [['user_get_id', 'date'], 'required'],
            [['user_get_id', 'user_give_id'], 'integer'],
            [['date'], 'safe'],
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
     * Gets query for [[HistoryObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistoryObjects()
    {
        return $this->hasMany(HistoryObject::className(), ['history_transaction_id' => 'id']);
    }
}
