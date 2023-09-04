<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "responsible".
 *
 * @property int $id
 * @property int $people_id
 * @property int $document_order_id
 *
 * @property DocumentOrder $documentOrder
 * @property People $people
 */
class Responsible extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'responsible';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['people_id', 'document_order_id'], 'required'],
            [['people_id', 'document_order_id'], 'integer'],
            [['document_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['document_order_id' => 'id']],
            [['people_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['people_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'people_id' => 'People ID',
            'document_order_id' => 'Document Order ID',
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
     * Gets query for [[People]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasOne(People::className(), ['id' => 'people_id']);
    }
}
