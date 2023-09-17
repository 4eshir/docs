<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "document_order_supplement".
 *
 * @property int $id
 * @property int $document_order_id
 * @property int $foreign_event_goals_id уставная цель
 * @property int $compliance_document 0 - отсутствует 1 - регламент 2 - письмо 3 - положение
 * @property string|null $document_details реквизиты регламента/письма
 * @property int $information_deadline срок предоставления информации об участии
 * @property int $input_deadline срок для внесения информации
 * @property int $collector_id ответственный за сбор и предоставление информации
 * @property int $contributor_id ответственный за внесение в цсхд
 * @property int $methodologist_id ответственный за методический контроль
 * @property int $informant_id ответственный за информирование работников
 *
 * @property ForeignEventGoals $foreignEventGoals
 * @property People $collector
 * @property People $contributor
 * @property People $informant
 * @property People $methodologist
 * @property DocumentOrder $documentOrder
 */
class DocumentOrderSupplement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_order_supplement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['document_order_id', 'foreign_event_goals_id', 'compliance_document', 'information_deadline', 'input_deadline', 'collector_id', 'contributor_id', 'methodologist_id', 'informant_id'], 'required'],
            [['document_order_id', 'foreign_event_goals_id', 'compliance_document', 'information_deadline', 'input_deadline', 'collector_id', 'contributor_id', 'methodologist_id', 'informant_id'], 'integer'],
            [['document_details'], 'string', 'max' => 1000],
            [['foreign_event_goals_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventGoals::className(), 'targetAttribute' => ['foreign_event_goals_id' => 'id']],
            [['collector_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['collector_id' => 'id']],
            [['contributor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['contributor_id' => 'id']],
            [['informant_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['informant_id' => 'id']],
            [['methodologist_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['methodologist_id' => 'id']],
            [['document_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['document_order_id' => 'id']],
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
            'foreign_event_goals_id' => 'Foreign Event Goals ID',
            'compliance_document' => 'Compliance Document',
            'document_details' => 'Document Details',
            'information_deadline' => 'Information Deadline',
            'input_deadline' => 'Input Deadline',
            'collector_id' => 'Collector ID',
            'contributor_id' => 'Contributor ID',
            'methodologist_id' => 'Methodologist ID',
            'informant_id' => 'Informant ID',
        ];
    }

    /**
     * Gets query for [[ForeignEventGoals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEventGoals()
    {
        return $this->hasOne(ForeignEventGoals::className(), ['id' => 'foreign_event_goals_id']);
    }

    /**
     * Gets query for [[Collector]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCollector()
    {
        return $this->hasOne(People::className(), ['id' => 'collector_id']);
    }

    /**
     * Gets query for [[Contributor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContributor()
    {
        return $this->hasOne(People::className(), ['id' => 'contributor_id']);
    }

    /**
     * Gets query for [[Informant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInformant()
    {
        return $this->hasOne(People::className(), ['id' => 'informant_id']);
    }

    /**
     * Gets query for [[Methodologist]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMethodologist()
    {
        return $this->hasOne(People::className(), ['id' => 'methodologist_id']);
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
}
