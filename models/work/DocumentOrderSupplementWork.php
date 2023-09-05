<?php

namespace app\models\work;

use app\models\common\DocumentOrder;
use app\models\common\DocumentOrderSupplement;
use app\models\work\PeopleWork;
use Yii;

class DocumentOrderSupplementWork extends DocumentOrderSupplement
{
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'document_order_id' => 'Document Order ID',
            'foreign_event_goals_id' => 'Уставная цель',
            'compliance_document' => 'Документ о мероприятии',
            'document_details' => 'Реквизиты документа',
            'information_deadline' => 'Срок предоставления информации (в днях)',
            'input_deadline' => 'Срок внесения информации (в днях)',
            'collector_id' => 'Ответственный за сбор и предоставление информации',
            'contributor_id' => 'Ответственный за внесение в ЦСХД',
            'methodologist_id' => 'Ответственный за методологический контроль',
            'informant_id' => 'Ответственный за информирование работников',
        ];
    }

    public function getForeignEventGoalsWork()
    {
        return $this->hasOne(ForeignEventGoalsWork::className(), ['id' => 'foreign_event_goals_id']);
    }

    public function getCollectorWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'collector_id']);
    }

    public function getContributorWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'contributor_id']);
    }

    public function getInformantWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'informant_id']);
    }

    public function getMethodologistWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'methodologist_id']);
    }

    public function getDocumentOrderWork()
    {
        return $this->hasOne(DocumentOrderWork::className(), ['id' => 'document_order_id']);
    }

}
