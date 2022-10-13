<?php

namespace app\models\work;

use app\models\common\Entry;
use Yii;


class EntryWork extends Entry
{
	public function getObjectWork()
    {
        return $this->hasOne(MaterialObjectWork::className(), ['id' => 'object_id']);
    }
}
