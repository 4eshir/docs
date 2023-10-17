<?php

namespace app\models\work;

use app\models\common\Company;
use app\models\common\Contract;
use app\models\common\Invoice;
use app\models\work\EntryWork;
use app\models\work\InvoiceEntryWork;
use app\models\work\CompanyWork;
use yii\helpers\Html;
use app\models\components\FileWizard;
use Yii;


class InvoiceWork extends Invoice
{
	public $objects; //записи об объектах
    public $documentFile; //документ основания

	public function rules()
    {
        return [
            [['number', 'contractor_id', 'date_invoice'], 'required'],
            [['contractor_id', 'type'], 'integer'],
            [['date_product', 'date_invoice'], 'safe'],
            [['number'], 'string', 'max' => 15],
            [['document'], 'string', 'max' => 1000],
            [['contractor_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['contractor_id' => 'id']],
            [['contract_id'], 'exist', 'skipOnError' => true, 'targetClass' => Contract::className(), 'targetAttribute' => ['contract_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'number' => 'Номер документа',
            'numberString' => 'Номер документа',
            'contractor_id' => 'Контрагент',
            'contractorString' => 'Контрагент',
            'contractorLink' => 'Контрагент',
            'date_product' => 'Дата приема товара/материального объекта в накладной',
            'date_invoice' => 'Дата документа',
            'type' => 'Type',
            'entries' => '',
            'documentFile' => 'Документ основания поступления',
            'documentLink' => 'Документ основания поступления',
            'contract_id' => 'Договор',
            'contractString' => 'Договор',
            'contractLink' => 'Договор',
        ];
    }


    public function getContractorString()
    {
        $contractor = CompanyWork::find()->where(['id' => $this->contractor_id])->one();
        //return CompanyWork::find()->where(['id' => $this->contractor_id])->createCommand()->getRawSql();
        return $contractor->name;
    }

    public function getContractorLink()
    {
        $contractor = CompanyWork::find()->where(['id' => $this->contractor_id])->one();
        return Html::a($contractor->name, \yii\helpers\Url::to(['company/view', 'id' => $contractor->id]));
    }

    public function getContractString()
    {
        $contract = ContractWork::find()->where(['id' => $this->contract_id])->one();
        return $contract->contractFullName;
    }

    public function getContractLink()
    {
        $contract = ContractWork::find()->where(['id' => $this->contract_id])->one();
        return Html::a($contract->contractFullName, \yii\helpers\Url::to(['contract/view', 'id' => $contract->id]));
    }

    public function getNumberString()
    {
        $type = $this->type;
        $name = ['Накладная', 'Акт', 'УПД', 'Протокол'];
        return Html::a($name[$type] . ' №' . $this->number, \yii\helpers\Url::to(['invoice/view', 'id' => $this->id]));
    }

    public function getDocumentLink()
    {
        return Html::a($this->document, \yii\helpers\Url::to(['invoice/get-file', 'fileName' => $this->document, 'modelId' => $this->id, 'type' => 'document']));
    }

    public function getEntries()
    {
        $entries = InvoiceEntryWork::find()->where(['invoice_id' => $this->id])->all();
        $result = '';
        foreach ($entries as $entry)
        {
            $objects = \app\models\work\ObjectEntryWork::find()->where(['entry_id' => $entry->entry_id])->orderBy(['id' => 'SORT_ASC'])->all();
            $symbol = null;
            if ($objects[0]->materialObject->complex == 1)
                $symbol = '<div class="hoverless" data-html="true" style="width: 26px; height: 26px; padding: 3px; margin-right: 5px; margin-top: 2px; background: #0d6efd; color: white; text-align: center; display: inline-block; border-radius: 4px" title="Составной объект">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-back" viewBox="0 0 16 16">
                    <path d="M0 2a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v2h2a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2v-2H2a2 2 0 0 1-2-2V2zm2-1a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h8a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H2z"/>
                </svg></div>';
            else
                $symbol = '<div class="hoverless" data-html="true" style="width: 26px; height: 26px; padding: 3px; margin-right: 5px; margin-top: 2px; background: #09ab3f; color: white; text-align: center; display: inline-block; border-radius: 4px" title="Простой объект">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-app" viewBox="0 0 16 16">
                      <path d="M11 2a3 3 0 0 1 3 3v6a3 3 0 0 1-3 3H5a3 3 0 0 1-3-3V5a3 3 0 0 1 3-3h6zM5 1a4 4 0 0 0-4 4v6a4 4 0 0 0 4 4h6a4 4 0 0 0 4-4V5a4 4 0 0 0-4-4H5z"/>
                    </svg>
                </svg></div>';

            $result .= $symbol.'<b>'.$objects[0]->materialObject->name.'</b> '.' ('.$objects[0]->materialObject->attribute.') - '.$entry->entry->amount.' шт.'.'<br>';

            $i = 1;
            foreach ($objects as $object)
            {
                $result .= $i .'. '. Html::a($object->materialObject->name, \yii\helpers\Url::to(['material-object/view', 'id' => $object->materialObject->id]));
                if ($object->materialObject->attribute === "ОС")
                    $result .= ' (инв. номер: '.$object->materialObject->inventory_number.')';
                $result .= ' - МОЛ: '. $object->materialObjectWork->MOL .'<br>';
                $i++;
            }
            $result .= '<hr style="border-top: 1px solid gray; margin-top: 5px; margin-bottom: 5px">';
        }
        return $result;
    }

    public function getErrorsWork()
    {
        $errorsList = InvoiceErrorsWork::find()->where(['invoice_id' => $this->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
        $result = '';
        foreach ($errorsList as $errors)
        {
            $errorName = ErrorsWork::find()->where(['id' => $errors->errors_id])->one();
            if ($errors->getCritical() == 1)
                $result .= 'Внимание, КРИТИЧЕСКАЯ ошибка: ' . $errorName->number . ' ' . $errorName->name . '<br>';
            else $result .= 'Внимание, ошибка: ' . $errorName->number . ' ' . $errorName->name . '<br>';
        }
        return $result;
    }

    public function uploadDocument()
    {
        $path = '@app/upload/files/invoice/document/';
        $filename = '';
        $filename = 'Нкл.'.$this->number.'_'.$this->id;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = FileWizard::CutFilename($res);
        $this->document = $res.'.'.$this->documentFile->extension;
        $this->documentFile->saveAs( $path.$res.'.'.$this->documentFile->extension);
    }

    public function beforeSave($insert)
    {
        $duplicate = InvoiceWork::find()->where(['date_invoice' => $this->date_invoice])->andWhere(['number' => $this->number])
            ->andWhere(['contractor_id' => $this->contractor_id])->andWhere(['!=', 'id', $this->id])->one();
        if ($insert) $duplicate = InvoiceWork::find()->where(['date_invoice' => $this->date_invoice])->andWhere(['number' => $this->number])
            ->andWhere(['contractor_id' => $this->contractor_id])->one();

        if ($duplicate !== null)
        {
            Yii::$app->session->setFlash('danger', 'Невозможно сохранить документ, т.к. в системе существует документ с таким же номером, датой и контрагентом');
            return false;
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
    	parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

    	if ($this->objects !== null && $this->objects[0]->name != '')
    	{
    	    //сохраняем все объекты из динамической формы
    		foreach ($this->objects as $object)
            {
                //создаем запись для накладной
                $entry = new EntryWork();
                $entry->amount = $object->amount;
                $entry->save();

                // создаем материальные объекты и связку
                for ($i = 0; $i < $object->amount; $i++)
                {
                    //var_dump($this->objects);
                    
                    //var_dump($newObject->id);

                    $newObjectEntry = new ObjectEntryWork();
                    $newObjectEntry->entry_id = $entry->id;
                    $newObjectEntry->material_object_id = $newObject->id;
                    $newObjectEntry->save();
                    $tempId = $newObjectEntry->id;

                    $newObject = new MaterialObjectWork($object);
                    $newObject->objEntrId = $tempId;
                    $newObject->characteristics = $object->characteristics;
                    $newObject->filesTmp = $_FILES["MaterialObjectWork"]["tmp_name"][$i]["characteristics"];
                    $newObject->filesName = $_FILES["MaterialObjectWork"]["name"][$i]["characteristics"];
                    $newObject->save();

                    $newObjectEntry = ObjectEntryWork::find()->where(['id' => $tempId])->one();
                    $newObjectEntry->material_object_id = $newObject->id;
                    $newObjectEntry->save();
                }

                //связываем запись и накладную/акт
                $invoiceEntry = InvoiceEntryWork::find()->where(['invoice_id' => $this->id])->andWhere(['entry_id' => $entry->id])->one();
                if ($invoiceEntry == null) $invoiceEntry = new InvoiceEntryWork();
                $invoiceEntry->invoice_id = $this->id;
                $invoiceEntry->entry_id = $entry->id;
                $invoiceEntry->save();
            }


    	}

        $errorsCheck = new InvoiceErrorsWork();
        $errorsCheck->CheckErrorsInvoiceWithoutAmnesty($this->id);
    }

    public function beforeDelete()
    {
        $invoiceEntries = InvoiceEntryWork::find()->where(['invoice_id' => $this->id])->all();

        foreach ($invoiceEntries as $one)
            $one->delete();

        $errors = InvoiceErrorsWork::find()->where(['invoice_id' => $this->id])->all();
        foreach ($errors as $error) $error->delete();

        return parent::beforeDelete();
    }

}
