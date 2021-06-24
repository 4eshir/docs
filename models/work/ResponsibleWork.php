<?php

namespace app\models\work;

use app\models\common\DocumentOrder;
use app\models\common\People;
use app\models\common\Responsible;
use Yii;


class ResponsibleWork extends Responsible
{
    public $fio;

    public function rules()
    {
        return [
            [['fio'], 'string'],
            [['people_id', 'document_order_id'], 'required'],
            [['people_id', 'document_order_id'], 'integer'],
            [['document_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['document_order_id' => 'id']],
            [['people_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['people_id' => 'id']],
        ];
    }

}
