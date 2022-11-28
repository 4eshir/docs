<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "expire".
 *
 * @property int $id
 * @property int $active_regulation_id
 * @property int|null $expire_regulation_id
 * @property int|null $expire_order_id
 * @property int $document_type_id
 * @property int $expire_type 1 - отмена, 2 - изменение
 *
 * @property DocumentOrder $activeRegulation
 * @property DocumentType $documentType
 * @property Regulation $expireRegulation
 * @property DocumentOrder $expireOrder
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
            [['active_regulation_id', 'document_type_id'], 'required'],
            [['active_regulation_id', 'expire_regulation_id', 'expire_order_id', 'document_type_id', 'expire_type'], 'integer'],
            [['active_regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['active_regulation_id' => 'id']],
            [['document_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentType::className(), 'targetAttribute' => ['document_type_id' => 'id']],
            [['expire_regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regulation::className(), 'targetAttribute' => ['expire_regulation_id' => 'id']],
            [['expire_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['expire_order_id' => 'id']],
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
            'expire_order_id' => 'Expire Order ID',
            'document_type_id' => 'Document Type ID',
            'expire_type' => 'Expire Type',
        ];
    }

    /**
     * Gets query for [[ActiveRegulation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getActiveRegulation()
    {
        return $this->hasOne(DocumentOrder::className(), ['id' => 'active_regulation_id']);
    }

    /**
     * Gets query for [[DocumentType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentType()
    {
        return $this->hasOne(DocumentType::className(), ['id' => 'document_type_id']);
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

    /**
     * Gets query for [[ExpireOrder]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpireOrder()
    {
        return $this->hasOne(DocumentOrder::className(), ['id' => 'expire_order_id']);
    }
}
