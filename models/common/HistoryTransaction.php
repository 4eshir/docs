<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "history_transaction".
 *
 * @property int $id
 * @property int $people_get_id получивший
 * @property int|null $people_give_id отдавший
 * @property string $date когда произошла передача объекта
 *
 * @property HistoryObject[] $historyObjects
 * @property People $peopleGet
 * @property People $peopleGive
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
            [['people_get_id', 'date'], 'required'],
            [['people_get_id', 'people_give_id'], 'integer'],
            [['date'], 'safe'],
            [['people_get_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['people_get_id' => 'id']],
            [['people_give_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['people_give_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'people_get_id' => 'People Get ID',
            'people_give_id' => 'People Give ID',
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

    /**
     * Gets query for [[PeopleGet]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeopleGet()
    {
        return $this->hasOne(People::className(), ['id' => 'people_get_id']);
    }

    /**
     * Gets query for [[PeopleGive]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeopleGive()
    {
        return $this->hasOne(People::className(), ['id' => 'people_give_id']);
    }
}
