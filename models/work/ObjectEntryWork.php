<?php

namespace app\models\work;

use app\models\common\ObjectEntry;
use app\models\null\EntryNull;
use app\models\null\MaterialObjectNull;
use Yii;


class ObjectEntryWork extends ObjectEntry
{
    public function getEntryWork()
    {
        $try = $this->hasOne(EntryWork::className(), ['id' => 'entry_id']);
        return $try->all() ? $try : new EntryNull();
    }


    public function getMaterialObjectWork()
    {
        $try = $this->hasOne(MaterialObjectWork::className(), ['id' => 'material_object_id']);
        return $try->all() ? $try : new MaterialObjectNull();
    }


    public function beforeDelete()
    {
        //$objects = MaterialObjectWork::find()->where(['id' => $this->material_object_id])->all();

        //foreach ($objects as $one)
        //    $one->delete();

        return parent::beforeDelete();
    }

}
