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


    public function beforeDelete()
    {
        $objects = MaterialObjectWork::find()->where(['id' => $this->material_object_id])->all();

        foreach ($objects as $one)
            $one->delete();

        return parent::beforeDelete();
    }

}
