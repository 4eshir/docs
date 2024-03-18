<?php

namespace app\models\work\order;

use app\models\DynamicModel;
use app\models\extended\ForeignEventParticipantsExtended;
use app\models\work\ExpireWork;
use app\models\work\ResponsibleWork;
use Yii;

class DocumentOrderRepository
{
    public function __construct()
    {
        
    }

    public function find($id)
    {
        return DocumentOrderWork::find()->where(['id' => $id])->one();
    }
}