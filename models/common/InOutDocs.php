<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "in_out_docs".
 *
 * @property int $id
 * @property int $document_in_id
 * @property int|null $document_out_id
 * @property string|null $date
 * @property int|null $people_id
 *
 * @property DocumentIn $documentIn
 * @property DocumentOut $documentOut
 */
class InOutDocs extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'in_out_docs';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['document_in_id'], 'required'],
            [['date'], 'string'],
            [['document_in_id', 'document_out_id', 'people_id'], 'integer'],
            [['document_in_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentIn::className(), 'targetAttribute' => ['document_in_id' => 'id']],
            [['document_out_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOut::className(), 'targetAttribute' => ['document_out_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'document_in_id' => 'Document In ID',
            'document_out_id' => 'Document Out ID',
        ];
    }

    /**
     * Gets query for [[DocumentIn]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentIn()
    {
        return $this->hasOne(DocumentIn::className(), ['id' => 'document_in_id']);
    }

    public function getPeople()
    {
        return $this->hasOne(People::className(), ['id' => 'people_id']);
    }

    /**
     * Gets query for [[DocumentOut]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOut()
    {
        return $this->hasOne(DocumentOut::className(), ['id' => 'document_out_id']);
    }
}
