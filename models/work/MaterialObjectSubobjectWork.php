<?php


namespace app\models\work;

use app\models\common\MaterialObjectSubobject;
use Yii;


class MaterialObjectSubobjectWork extends MaterialObjectSubobject
{

    public function getSubobjectWork()
    {
        return $this->hasOne(SubobjectWork::className(), ['id' => 'subobject_id']);
    }

    public function getMaterialObjectWork()
    {
        return $this->hasOne(MaterialObjectWork::className(), ['id' => 'material_object_id']);
    }

}