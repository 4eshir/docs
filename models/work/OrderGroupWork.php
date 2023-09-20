<?php

namespace app\models\work;

use app\models\common\OrderGroup;
use app\models\null\DocumentOrderNull;
use app\models\null\TrainingGroupNull;
use Yii;


class OrderGroupWork extends OrderGroup
{
    public function getDocumentOrderWork()
    {
        $try = $this->hasOne(DocumentOrderWork::className(), ['id' => 'document_order_id']);
        return $try->all() ? $try : new DocumentOrderNull();
    }

    public function getTrainingGroupWork()
    {
        $try = $this->hasOne(TrainingGroupWork::className(), ['id' => 'training_group_id']);
        return $try->all() ? $try : new TrainingGroupNull();
    }
}
