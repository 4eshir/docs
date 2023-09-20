<?php

namespace app\models\work;

use app\models\common\DocumentOrder;
use app\models\common\DocumentOrderSupplement;
use app\models\work\PeopleWork;
use app\models\null\ForeignEventGoalsNull;
use app\models\null\DocumentOrderNull;
use app\models\null\PeopleNull;
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
        $try = $this->hasOne(ForeignEventGoalsWork::className(), ['id' => 'foreign_event_goals_id']);
        return $try->all() ? $try : new ForeignEventGoalsNull();
    }

    public function getCollectorWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'collector_id']);
        return $try->all() ? $try : new PeopleNull();
    }

    public function getContributorWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'contributor_id']);
        return $try->all() ? $try : new PeopleNull();
    }

    public function getInformantWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'informant_id']);
        return $try->all() ? $try : new PeopleNull();
    }

    public function getMethodologistWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'methodologist_id']);
        return $try->all() ? $try : new PeopleNull();
    }

    public function getDocumentOrderWork()
    {
        $try = $this->hasOne(DocumentOrderWork::className(), ['id' => 'document_order_id']);
        return $try->all() ? $try : new DocumentOrderNull();
    }

}
