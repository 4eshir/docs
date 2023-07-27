<?php

namespace app\models\work;

use app\models\common\DocumentOrder;
use app\models\common\DocumentOrderSupplement;
use app\models\work\PeopleWork;
use Yii;

class DocumentOrderSupplementWork extends DocumentOrderSupplement
{
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
