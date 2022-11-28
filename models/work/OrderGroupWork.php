<?php

namespace app\models\work;

use app\models\common\OrderGroup;
use Yii;


class OrderGroupWork extends OrderGroup
{
    public function getDocumentOrderWork()
    {
        return $this->hasOne(DocumentOrderWork::className(), ['id' => 'document_order_id']);
    }

    public function getTrainingGroupWork()
    {
        return $this->hasOne(TrainingGroupWork::className(), ['id' => 'training_group_id']);
    }
}
