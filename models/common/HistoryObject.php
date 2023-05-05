<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "history_object".
 *
 * @property int $id
 * @property int $material_object_id
 * @property int $count
 * @property int|null $container_id
 * @property int $history_transaction_id
 *
 * @property HistoryTransaction $historyTransaction
 */
class HistoryObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'history_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['material_object_id', 'count', 'history_transaction_id'], 'required'],
            [['material_object_id', 'count', 'container_id', 'history_transaction_id'], 'integer'],
            [['history_transaction_id'], 'exist', 'skipOnError' => true, 'targetClass' => HistoryTransaction::className(), 'targetAttribute' => ['history_transaction_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'material_object_id' => 'Material Object ID',
            'count' => 'Count',
            'container_id' => 'Container ID',
            'history_transaction_id' => 'History Transaction ID',
        ];
    }

    /**
     * Gets query for [[HistoryTransaction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistoryTransaction()
    {
        return $this->hasOne(HistoryTransaction::className(), ['id' => 'history_transaction_id']);
    }
}
