<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "order_group".
 *
 * @property int $id
 * @property int $document_order_id
 * @property int $training_group_id
 * @property string|null $comment
 *
 * @property DocumentOrder $documentOrder
 * @property TrainingGroup $trainingGroup
 */
class OrderGroup extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_group';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['document_order_id', 'training_group_id'], 'integer'],
            [['comment'], 'string'],
            [['document_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['document_order_id' => 'id']],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroup::className(), 'targetAttribute' => ['training_group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'document_order_id' => 'Номер и название приказа',
            'training_group_id' => 'Training Group ID',
            'comment' => 'Примечание'
        ];
    }

    /**
     * Gets query for [[DocumentOrder]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOrder()
    {
        return $this->hasOne(DocumentOrder::className(), ['id' => 'document_order_id']);
    }

    /**
     * Gets query for [[TrainingGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroup()
    {
        return $this->hasOne(TrainingGroup::className(), ['id' => 'training_group_id']);
    }
}
