<?php

namespace app\models\work;

use Yii;
use app\models\common\MaterialObjectErrors;

class MaterialObjectErrorsWork extends MaterialObjectErrors
{
    public function MaterialObjectAmnesty ($modelMaterialObjectID)
    {
        $errors = MaterialObjectErrorsWork::find()->where(['material_object_id' => $modelMaterialObjectID, 'time_the_end' => null, 'amnesty' => null])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = 1;
            $err->save();
        }
    }

    private function NoAmnesty ($modelMaterialObjectID)
    {
        $errors = MaterialObjectErrorsWork::find()->where(['material_object_id' => $modelMaterialObjectID, 'time_the_end' => null, 'amnesty' => 1])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = null;
            $err->save();
        }
    }

    public function getCritical()
    {
        return $this->critical;
    }

    private function CheckContainer ($modelMaterialObjectID)
    {
        $err = MaterialObjectErrorsWork::find()->where(['material_object_id' => $modelMaterialObjectID, 'time_the_end' => null, 'errors_id' => 52])->all();
        $container = ContainerObjectWork::find()->where(['material_object_id' => $modelMaterialObjectID])->all();

        foreach ($err as $oneErr)
        {
            if (count($container) > 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && count($container) === 0)
        {
            $this->material_object_id = $modelMaterialObjectID;
            $this->errors_id = 52;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckContainerMaterialObject ($modelMaterialObjectID)
    {
        $this->CheckContainer($modelMaterialObjectID);
    }

    public function CheckErrorsMaterialObject ($modelMaterialObjectID)
    {
        //$materialObject = MaterialObjectWork::find()->where(['id' => $modelMaterialObjectID])->one();

        $this->CheckContainer($modelMaterialObjectID);
    }

    public function CheckErrorsMaterialObjectWithoutAmnesty ($modelMaterialObjectID)
    {
        $this->NoAmnesty($modelMaterialObjectID);
        $this->CheckErrorsMaterialObject($modelMaterialObjectID);
    }
}
