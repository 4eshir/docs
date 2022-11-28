<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "history_object".
 *
 * @property int $id
 * @property int $material_object_id
 * @property int $count
 * @property int $container_id
 * @property int $history_transaction_id
 *
 * @property MaterialObject $materialObject
 * @property HistoryObject $historyTransaction
 * @property HistoryObject[] $historyObjects
 * @property Container $container
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
            [['material_object_id', 'count', 'container_id', 'history_transaction_id'], 'required'],
            [['material_object_id', 'count', 'container_id', 'history_transaction_id'], 'integer'],
            [['material_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObject::className(), 'targetAttribute' => ['material_object_id' => 'id']],
            [['history_transaction_id'], 'exist', 'skipOnError' => true, 'targetClass' => HistoryObject::className(), 'targetAttribute' => ['history_transaction_id' => 'id']],
            [['container_id'], 'exist', 'skipOnError' => true, 'targetClass' => Container::className(), 'targetAttribute' => ['container_id' => 'id']],
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
     * Gets query for [[MaterialObject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialObject()
    {
        return $this->hasOne(MaterialObject::className(), ['id' => 'material_object_id']);
    }

    /**
     * Gets query for [[HistoryTransaction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistoryTransaction()
    {
        return $this->hasOne(HistoryObject::className(), ['id' => 'history_transaction_id']);
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
     * Gets query for [[Container]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContainer()
    {
        return $this->hasOne(Container::className(), ['id' => 'container_id']);
    }
}
