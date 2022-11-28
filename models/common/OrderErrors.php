<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "order_errors".
 *
 * @property int $id
 * @property int $document_order_id
 * @property int $errors_id
 * @property string $time_start
 * @property string|null $time_the_end
 * @property int|null $critical
 * @property int|null $amnesty
 *
 * @property DocumentOrder $documentOrder
 * @property Errors $errors0
 */
class OrderErrors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_errors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['document_order_id', 'errors_id', 'time_start'], 'required'],
            [['document_order_id', 'errors_id', 'critical', 'amnesty'], 'integer'],
            [['time_start', 'time_the_end'], 'safe'],
            [['document_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['document_order_id' => 'id']],
            [['errors_id'], 'exist', 'skipOnError' => true, 'targetClass' => Errors::className(), 'targetAttribute' => ['errors_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'document_order_id' => 'Document Order ID',
            'errors_id' => 'Errors ID',
            'time_start' => 'Time Start',
            'time_the_end' => 'Time The End',
            'critical' => 'Critical',
            'amnesty' => 'Amnesty',
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
     * Gets query for [[Errors0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getErrors0()
    {
        return $this->hasOne(Errors::className(), ['id' => 'errors_id']);
    }
}
