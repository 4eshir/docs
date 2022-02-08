<?php

namespace app\models\work;

use Yii;
use app\models\common\OrderErrors;
use app\models\work\ErrorsWork;


class OrderErrorsWork extends OrderErrors
{
    public function OrderAmnesty ($modelOrderID)
    {
        $errors = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'amnesty' => null])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = 1;
            $err->save();
        }
    }

    private function NoAmnesty ($modelOrderID)
    {
        $errors = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'amnesty' => 1])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = null;
            $err->save();
        }
    }

    /*-------------------------------------------------*/

    private function CheckScan ($modelOrderID, $order)
    {
        $err = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'errors_id' => 19])->all();

        foreach ($err as $oneErr)
        {
            if ($order->scan != null)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $order->scan == null)
        {
            $this->document_order_id = $modelOrderID;
            $this->errors_id = 19;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckDocument ($modelOrderID, $order)
    {
        $err = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'errors_id' => 20])->all();

        foreach ($err as $oneErr)
        {
            if ($order->doc != null)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $order->doc == null)
        {
            $this->document_order_id = $modelOrderID;
            $this->errors_id = 20;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckKeyWord ($modelOrderID, $order)
    {
        $err = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'errors_id' => 21])->all();

        foreach ($err as $oneErr)
        {
            if ($order->key_words != null)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $order->key_words == null)
        {
            $this->document_order_id = $modelOrderID;
            $this->errors_id = 21;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckGroup ($modelOrderID, $order)
    {
        $err = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'errors_id' => 22])->all();
        $group = OrderGroupWork::find()->where(['document_order_id' => $modelOrderID])->all();

        foreach ($err as $oneErr)
        {
            if (count($group) !== 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $order->key_words == null)
        {
            $this->document_order_id = $modelOrderID;
            $this->errors_id = 22;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    /*-------------------------------------------------*/

    public function CheckDocumentOrder ($modelOrderID)
    {
        $order = DocumentOrderWork::find()->where(['id' => $modelOrderID])->one();
        $this->CheckScan($modelOrderID, $order);
        $this->CheckDocument($modelOrderID, $order);
        $this->CheckKeyWord($modelOrderID, $order);
        if ($order->type == 0 || $order->type == 11)    // учебный
            $this->CheckGroup($modelOrderID, $order);
    }

    public function CheckErrorsDocumentOrderWithoutAmnesty ($modelOrderID)
    {
        $this->NoAmnesty($modelOrderID);
        $this->CheckDocumentOrder($modelOrderID);
    }

}
