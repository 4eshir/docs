<?php

namespace app\models\work;

use app\models\common\DocumentOrderSupplement;
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
        $err = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'errors_id' => 17])->all();

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
            $this->errors_id = 17;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckDocument ($modelOrderID, $order)
    {
        $err = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'errors_id' => 18])->all();

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
            $this->errors_id = 18;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckKeyWord ($modelOrderID, $order)
    {
        $err = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'errors_id' => 19])->all();

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
            $this->errors_id = 19;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckGroup ($modelOrderID)
    {
        $err = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'errors_id' => 20])->all();
        $group = OrderGroupWork::find()->where(['document_order_id' => $modelOrderID])->all();

        foreach ($err as $oneErr)
        {
            if (count($group) !== 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && count($group) === 0)
        {
            $this->document_order_id = $modelOrderID;
            $this->errors_id = 20;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckPasta ($modelOrderID)
    {
        $err = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'errors_id' => 37])->all();
        $pastaCount = count(OrderGroupParticipantWork::find()->joinWith(['orderGroup orderGroup'])->where(['orderGroup.document_order_id' => $modelOrderID])->all());

        foreach ($err as $oneErr)
        {
            if ($pastaCount !== 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }
        //boobs
        if (count($err) == 0 && $pastaCount == 0)
        {
            $this->document_order_id = $modelOrderID;
            $this->errors_id = 37;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckForeignEvent ($modelOrderID)
    {
        $err = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'errors_id' => 57])->all();
        $foreignEventCount = count(ForeignEventWork::find()->where(['order_participation_id' => $modelOrderID])->all());

        foreach ($err as $oneErr)
        {
            if ($foreignEventCount !== 0)
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $foreignEventCount == 0)
        {
            $this->document_order_id = $modelOrderID;
            $this->errors_id = 57;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckSupplement ($modelOrderID)
    {
        $err = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'errors_id' => 58])->all();
        $supplementCount = count(DocumentOrderSupplementWork::find()->where(['document_order_id' => $modelOrderID])->all());

        foreach ($err as $oneErr)
        {
            if ($supplementCount !== 0)
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) == 0 && $supplementCount == 0)
        {
            $this->document_order_id = $modelOrderID;
            $this->errors_id = 58;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    /*-------------------------------------------------*/

    public function CheckDocumentOrder ($modelOrderID)
    {
        $order = DocumentOrderWork::find()->where(['id' => $modelOrderID])->one();
        $this->CheckScan($modelOrderID, $order);
        if ($order->type === 1 || $order->type == 10)   // неучебный
            $this->CheckDocument($modelOrderID, $order);
        $this->CheckKeyWord($modelOrderID, $order);
        if ($order->type == 0 || $order->type == 11)    // учебный
        {
            $this->CheckGroup($modelOrderID);
            if ($order->order_date >= "2022-03-01")
                $this->CheckPasta($modelOrderID);
        }
        if ($order->type === 2)
        {
            $this->CheckForeignEvent($modelOrderID);
            $this->CheckSupplement($modelOrderID);
        }
    }

    public function CheckErrorsDocumentOrderWithoutAmnesty ($modelOrderID)
    {
        $this->NoAmnesty($modelOrderID);
        $this->CheckDocumentOrder($modelOrderID);
    }

    public function PermissionToParticipate ($modelOrderID)
    {
        $err = OrderErrorsWork::find()->where(['document_order_id' => $modelOrderID, 'time_the_end' => null, 'amnesty' => null])
            ->andWhere(['IN', 'errors_id', [57, 58]])->all();
        if (count($err) == null)
            return true;
        else
            return false;
    }

}
