<?php

namespace app\models\work;

use app\models\common\BackupVisit;
use Yii;


class BackupVisitWork extends BackupVisit
{
    public function __construct($id,/* $foreign_event_participant_id, $training_group_participant_id, $status*/ $structure)
    {
        $this->id = $id;
        $this->structure = $structure;
    }
}
