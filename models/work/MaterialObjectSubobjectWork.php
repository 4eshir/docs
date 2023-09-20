<?php


namespace app\models\work;

use app\models\common\MaterialObjectSubobject;
use app\models\null\MaterialObjectNull;
use app\models\null\SubobjectNull;
use Yii;


class MaterialObjectSubobjectWork extends MaterialObjectSubobject
{

    public function getSubobjectWork()
    {
        $try = $this->hasOne(SubobjectWork::className(), ['id' => 'subobject_id']);
        return $try->all() ? $try : new SubobjectNull();
    }

    public function getMaterialObjectWork()
    {
        $try = $this->hasOne(MaterialObjectWork::className(), ['id' => 'material_object_id']);
        return $try->all() ? $try : new MaterialObjectNull();
    }


    public function beforeDelete()
    {
        
        return parent::beforeDelete();
    }

}