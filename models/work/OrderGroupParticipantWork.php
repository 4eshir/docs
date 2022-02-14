<?php

namespace app\models\work;

use app\models\common\OrderGroupParticipant;
use Yii;


class OrderGroupParticipantWork extends OrderGroupParticipant
{
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_group_id' => 'Order Group ID',
            'group_participant_id' => 'Group Participant ID',
            'status' => 'Status',   // 0 - зачисление; 1 - отчисление; 2 - перевод
        ];
    }
}
