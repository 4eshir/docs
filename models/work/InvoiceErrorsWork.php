<?php

namespace app\models\work;

use Yii;
use app\models\common\InvoiceErrors;

class InvoiceErrorsWork extends InvoiceErrors
{
    public function InvoiceAmnesty ($modelInvoiceID)
    {
        $errors = InvoiceErrorsWork::find()->where(['invoice_id' => $modelInvoiceID, 'time_the_end' => null, 'amnesty' => null])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = 1;
            $err->save();
        }
    }

    private function NoAmnesty ($modelInvoiceID)
    {
        $errors = InvoiceErrorsWork::find()->where(['invoice_id' => $modelInvoiceID, 'time_the_end' => null, 'amnesty' => 1])->all();
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

    private function CheckContract ($modelInvoiceID, $invoice)
    {
        $err = InvoiceErrorsWork::find()->where(['invoice_id' => $modelInvoiceID, 'time_the_end' => null, 'errors_id' => 50])->all();

        foreach ($err as $oneErr)
        {
            if ($invoice->contract_id !== '')     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $invoice->contract_id === '')
        {
            $this->invoice_id = $modelInvoiceID;
            $this->errors_id = 50;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckScan ($modelInvoiceID, $invoice)
    {
        $err = InvoiceErrorsWork::find()->where(['invoice_id' => $modelInvoiceID, 'time_the_end' => null, 'errors_id' => 49])->all();

        foreach ($err as $oneErr)
        {
            if ($invoice->document !== '')     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $invoice->document === '')
        {
            $this->invoice_id = $modelInvoiceID;
            $this->errors_id = 49;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckEntry ($modelInvoiceID)
    {
        $err = InvoiceErrorsWork::find()->where(['invoice_id' => $modelInvoiceID, 'time_the_end' => null, 'errors_id' => 51])->all();
        $entry = InvoiceEntryWork::find()->where(['invoice_id' => $modelInvoiceID])->all();

        foreach ($err as $oneErr)
        {
            if (count($entry) > 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && count($entry) === 0)
        {
            $this->invoice_id = $modelInvoiceID;
            $this->errors_id = 51;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckErrorsInvoice ($modelInvoiceID)
    {
        $invoice = InvoiceWork::find()->where(['id' => $modelInvoiceID])->one();

        $this->CheckContract($modelInvoiceID, $invoice);
        $this->CheckScan($modelInvoiceID, $invoice);
        $this->CheckEntry($modelInvoiceID);
    }

    public function CheckErrorsInvoiceWithoutAmnesty ($modelInvoiceID)
    {
        $this->NoAmnesty($modelInvoiceID);
        $this->CheckErrorsInvoice($modelInvoiceID);
    }

}
