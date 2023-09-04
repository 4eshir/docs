<?php

namespace app\models\work;

use Yii;
use app\models\common\ContainerErrors;

class ContainerErrorsWork extends ContainerErrors
{
    public function ContainerAmnesty ($modelContainerID)
    {
        $errors = ContainerErrorsWork::find()->where(['container_id' => $modelContainerID, 'time_the_end' => null, 'amnesty' => null])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = 1;
            $err->save();
        }
    }

    private function NoAmnesty ($modelContainerID)
    {
        $errors = ContainerErrorsWork::find()->where(['container_id' => $modelContainerID, 'time_the_end' => null, 'amnesty' => 1])->all();
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

    private function CheckHierarchy ($modelContainerID, $container)
    {
        $err = ContainerErrorsWork::find()->where(['container_id' => $modelContainerID, 'time_the_end' => null, 'errors_id' => 53])->all();

        foreach ($err as $oneErr)
        {
            if ($container->container_id !== '')     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $container->container_id === '')
        {
            $this->container_id = $modelContainerID;
            $this->errors_id = 53;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckEmpty ($modelContainerID)
    {
        $err = ContainerErrorsWork::find()->where(['container_id' => $modelContainerID, 'time_the_end' => null, 'errors_id' => 54])->all();
        $objects = ContainerObjectWork::find()->where(['container_id' => $modelContainerID])->all();

        foreach ($err as $oneErr)
        {
            if (count($objects) > 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && count($objects) === 0)
        {
            $this->container_id = $modelContainerID;
            $this->errors_id = 54;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckErrorsContainer ($modelContainerID)
    {
        $container = ContainerWork::find()->where(['id' => $modelContainerID])->one();

        $this->CheckHierarchy($modelContainerID, $container);
        $this->CheckEmpty($modelContainerID);
    }

    public function CheckErrorsContainerWithoutAmnesty ($modelContainerID)
    {
        $this->NoAmnesty($modelContainerID);
        $this->CheckErrorsContainer($modelContainerID);
    }

}
