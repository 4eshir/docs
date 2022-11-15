<?php

namespace app\models\work;

use app\models\common\ObjectEntry;
use Yii;


class ObjectEntryWork extends ObjectEntry
{
    public function getEntryWork()
    {
        return $this->hasOne(EntryWork::className(), ['id' => 'entry_id']);
    }


    public function getMaterialObjectWork()
    {
        return $this->hasOne(MaterialObjectWork::className(), ['id' => 'material_object_id']);
    }


}
