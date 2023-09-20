<?php

namespace app\models\work;

use app\models\common\MaterialObject;
use app\models\null\FinanceSourceNull;
use app\models\null\KindObjectNull;
use app\models\null\ObjectEntryNull;
use app\models\work\ObjectCharacteristicWork;
use yii\web\UploadedFile;
use yii\helpers\Html;

use Yii;


class MaterialObjectWork extends MaterialObject
{
    public $photoFile; //поле для загрузки фотографии объекта
	public $expirationDate; //дата окончания срока годности
    public $characteristics; //список характеристик объекта

    public $amount; //количество объектов в записи накладной

    public $filesTmp; //файлы (при создании накладной)
    public $filesName; //файлы (при создании накладной)

    public $objEntrId; //id связки object-entry для именования файлов

    public $visionMOL; //видимость поля "МОЛ"
    public $molId; //id МОЛ-а

    function __construct($obj = null)
    {
        $this->id = $obj->id;
        $this->name = $obj->name;
        $this->photo_local = $obj->photo_local;
        $this->photo_cloud = $obj->photo_cloud;
        $this->count = $obj->count;
        $this->price = $obj->price;
        //$this->number = $obj->number;
        $this->attribute = $obj->attribute;
        $this->finance_source_id = $obj->finance_source_id;
        $this->inventory_number = $obj->inventory_number;
        $this->type = $obj->type;
        $this->kind_id = $obj->kind_id;
        $this->is_education = $obj->is_education;
        $this->state = $obj->state;
        $this->damage = $obj->damage;
        $this->status = $obj->status;
        $this->write_off = $obj->write_off;
        $this->lifetime = $obj->lifetime;
        $this->expiration_date = $obj->expiration_date;
        $this->create_date = $obj->create_date;
        $this->characteristics = $obj->characteristics;
    }

	public function rules()
    {
        return [
            //[['name', 'price', 'number', 'finance_source_id', 'type', 'is_education'], 'required'],
            [['count', 'type', 'is_education', 'state', 'status', 'write_off', 'expiration_date', 'kind_id', 'amount', 'complex', 'molId', 'visionMOL'], 'integer'],
            [['price'], 'double'],
            [['lifetime', 'create_date', 'characteristics', 'name', 'price', 'number', 'finance_source_id', 'type', 'is_education', 'filesTmp', 'filesName'], 'safe'],
            [['name', 'photo_local', 'photo_cloud', 'expirationDate'], 'string', 'max' => 1000],
            [['attribute'], 'string', 'max' => 3],
            [['inventory_number'], 'string', 'max' => 20],
            [['damage'], 'string', 'max' => 2000],
            [['finance_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => FinanceSourceWork::className(), 'targetAttribute' => ['finance_source_id' => 'id']],
            [['photoFile'], 'file', 'extensions' => 'jpg, jpeg, png, pdf, webp, jfif, 7z, rar, zip', 'skipOnEmpty' => true, 'maxSize' => 104857600]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование объекта',
            'nameLink' => 'Наименование объекта',
            'photo_local' => 'Фото объекта (low-res)',
            'photo_cloud' => 'Фото объекта (hi-res)',
            'photoFile' => 'Фото объекта',
            'count' => 'Количество',
            'amount' => 'Количество',
            'price' => 'Цена за единицу',
            'priceString' => 'Цена за единицу',
            'number' => 'Документ о поступлении',
            'numberLink' => 'Документ о поступлении материального объекта',
            'attribute' => 'Признак',
            'finance_source_id' => 'Источник финансирования',
            'financeSourceString' => 'Источник финансирования',
            'inventory_number' => 'Инвентарный номер',
            'type' => 'Тип объекта по расходованию',
            'typeString' => 'Тип объекта по расходованию',
            'is_education' => 'Является учебным материально-техническим ресурсом',
            'isEducationString' => 'Является учебным материально-техническим ресурсом',
            'state' => 'Остаток (в %)',
            'damage' => 'Описание повреждений (опционально)',
            'status' => 'Объект в работоспособном состоянии',
            'statusString' => 'Объект в работоспособном состоянии',
            'write_off' => 'Статус списания',
            'writeOffString' => 'Статус списания',
            'lifetime' => 'Ожидаемая дата окончания эксплуатации (опционально)',
            'expiration_date' => 'Срок годности (в днях)',
            'expirationDate' => 'Дата окончания срока годности (при наличии)',
            'create_date' => 'Дата производства объекта',
            'kind_id' => 'Класс объекта',
            'kindString' => 'Класс объекта',
            'complexString' => '',
            'atContainerLink' => 'Является контейнером',
            'inContainerLink' => 'Лежит в контейнере',
            'molId' => 'МОЛ',
            'MOL' => 'МОЛ',
        ];
    }

    public function getNameLink()
    {
        return Html::a($this->name, \yii\helpers\Url::to(['material-object/view', 'id' => $this->id]));
    }

    public function getNameAndNumberMaterialObject()
    {
        $result = $this->name;
        if ($this->inventory_number !== '' && $this->inventory_number !== null)
            $result .= ' (инв. № ' . $this->inventory_number . ')';
        else
            $result .= ' (внут. рег. № ' . $this->id . ')';
        return $result;
    }

    public function getKindWork()
    {
        $try = $this->hasOne(KindObjectWork::className(), ['id' => 'kind_id']);
        return $try->all() ? $try : new KindObjectNull();
    }

    public function getObjectEntryWork()
    {
        $try = $this->hasOne(ObjectEntryWork::className(), ['material_object_id' => 'id']);
        return $try->all() ? $try : new ObjectEntryNull();
    }

    public function getFinanceSourceWork()
    {
        $try = $this->hasOne(FinanceSourceWork::className(), ['id' => 'finance_source_id']);
        return $try->all() ? $try : new FinanceSourceNull();
    }

    public function getKindString()
    {
        $chars = ObjectCharacteristicWork::find()->where(['material_object_id' => $this->id])->orderBy(['characteristic_object_id' => SORT_ASC])->all();
        if (!empty($chars))
        {
            $res = '<div style="float: left; width: 20%; height: 100%; line-height: 250%">'.$this->kindWork->name.'</div><div style="float: left; width: 80%"><button class="accordion" style="display: flex; float: left">Показать характеристики</button><div class="panel">';
            $res .= '<table>';

            foreach ($chars as $char)
            {
                $res .= '<tr><td style="padding-right: 15px; padding-bottom: 2px; width: 80%;">'.$char->characteristicObjectWork->name.'</td>';
                if ($char->characteristicObjectWork->value_type == 4)
                    $res .= '<td>'.($char->getValue() == 1 ? 'Да' : 'Нет').'</td>';
                else
                    $res .= '<td>'.$char->getValue().'</td>';
            }
            $res .= '</table></div></div>';
        }
        else
            $res = '<span class="not-set">(не задано)</span>';

        return $res;
    }

    public function getTypeString()
    {
        return $this->type == 1 ? 'Нерасходуемый' : 'Расходуемый';
    }

    public function getIsEducationString()
    {
        return $this->is_education == 1 ? 'Да' : 'Нет';
    }

    public function getStatusString()
    {
        return $this->status == 1 ? 'Рабочий' : 'Нерабочий';
    }

    public function getFinanceSourceString()
    {
        return $this->financeSourceWork->name;
    }

    public function getPriceString()
    {
        return $this->price . ' ₽';
    }

    public function getAtContainerLink()
    {
        $container = ContainerWork::find()->where(['material_object_id' => $this->id])->one();
        return Html::a($container->name, \yii\helpers\Url::to(['container/view', 'id' => $container->id]));
    }

    public function getInContainerLink()
    {
        $result = '';
        $containerIN = ContainerObjectWork::find()->where(['material_object_id' => $this->id])->one();
        $container = ContainerWork::find();
        $step = $containerIN->container_id;
        while ($step !== null)
        {
            $containerNext = $container->where(['id' => $step])->one();
            $result .= Html::a($containerNext->name, \yii\helpers\Url::to(['container/view', 'id' => $containerNext->id])) . ' -> ';
            $step = $containerNext->container_id;
        }
        $result = substr($result,0,-4);
        return $result;
    }

    public function getNumberLink()
    {
        $entry = ObjectEntryWork::find()->where(['material_object_id' => $this->id])->one();
        $invoice = InvoiceEntryWork::find()->where(['entry_id' => $entry->entry_id])->one();

        $type = $invoice->invoiceWork->type;
        $name = ['Накладная', 'Акт', 'УПД', 'Протокол'];

        $fullName = $name[$type] . ' №' . $invoice->invoiceWork->number;

        return Html::a($fullName, \yii\helpers\Url::to(['invoice/view', 'id' => $invoice->invoiceWork->id]));
    }

    public function getMOL()
    {
        $hist_obj = HistoryObjectWork::find()->where(['material_object_id' => $this->id])->orderBy(['id' => SORT_DESC])->one();
        $hist_trans = HistoryTransactionWork::find()->where(['id' => $hist_obj->history_transaction_id])->one();

        return Html::a($hist_trans->peopleGetWork->fullName, \yii\helpers\Url::to(['people/view', 'id' => $hist_trans->people_get_id]));
    }

    public function getWriteOffString()
    {
        if ($this->write_off == 0)
            return 'Доступен для эксплуатации';
        return $this->write_off == 1 ? 'Готов к списанию' : 'Списан';
    }

    public function getComplexString()
    {
        $parentObj = MaterialObjectSubobjectWork::find()->where(['material_object_id' => $this->id])->all();
        $res = '';
        if ($parentObj !== null)
        {
            $res .= '<tr style="width: 30px; font-weight: 600;"><td style="width: 6%;">№ п/п</td><td>Название компонентов</td><td>Описание</td><td>Состояние</td></tr>';
            $i = 1;
            foreach ($parentObj as $one)
            {
                $res .= '<tr><td>'.$i.'</td><td>'.$one->subobjectWork->name.'</td><td>'.$one->subobjectWork->characteristics.'</td><td>'.$one->subobjectWork->stateString.'</td></tr>';
                $subs = SubobjectWork::find()->where(['parent_id' => $one->subobjectWork->id])->all();
                if ($subs !== null)
                {
                    $j = 1;
                    foreach ($subs as $sub)
                    {
                        $res .= '<tr><td>'.$i.'.'.$j.'</td><td>'.$sub->name.'</td><td>'.$sub->characteristics.'</td><td>'.$sub->stateString.'</td></tr>';
                        $j++;
                    }
                }
                $i++;
            }
        }

        return $res;
    }

    public function beforeSave($insert)
    {
        if ($this->expirationDate == 0)
        {
            $this->expiration_date = 0;
            return parent::beforeSave($insert);
        }
    	$d1 = strtotime($this->expirationDate);
    	$d2 = strtotime($this->create_date);
    	$this->expiration_date = round(($d1 - $d2) / (60 * 60 * 24));
    	return parent::beforeSave($insert);
    }

    //проверка на единственную запись в истории
    public function isOnlyEntry()
    {
        return true;
    }

    public function transferProcess($user_from, $user_to, $date)
    {
        $history_obj = HistoryObjectWork::find()->where(['material_object_id' => $this->id])->orderBy(['id' => SORT_DESC])->one();

        if ($history_obj !== null) $trans = HistoryTransactionWork::find()->where(['id' => $history_obj->history_transaction_id])->one();
        if ($trans !== null && $trans->people_get_id == $user_to) return;

        if ($trans !== null && $this->isOnlyEntry())
        {
            $trans->people_get_id = $user_to;
            $trans->save();
            return;
        }

        $history_obj = new HistoryObjectWork();
        $history_obj->material_object_id = $this->id;
        $history_obj->count = 1;
        $curCont = ContainerObjectWork::find()->where(['material_object_id' => $this->id])->one();
        if ($curCont === null) $history_obj->container_id = null;
        else $history_obj->container_id = $curCont->container_id;

        $history_trans = new HistoryTransactionWork();
        $history_trans->people_give_id = $user_from;
        $history_trans->people_get_id = $user_to;
        $history_trans->date = $date;
        $history_trans->save();

        $history_obj->history_transaction_id = $history_trans->id;
        $history_obj->save();
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->transferProcess(null, $this->molId, '2023-05-05');


        $characts = KindCharacteristicWork::find()->where(['kind_object_id' => $this->kindWork->id])->orderBy(['characteristic_object_id' => SORT_ASC])->all();

        //Создаем файл на сервере (файлы идут по порядку, [0], [1], [2]...). Если файла нет - то там будет "", но элемент в массиве присутствует
        $fileTmpPath = $_FILES["EntryWork"]["tmp_name"]["characteristics"];

        //получаем данные если файлы из динамической формы накладной
        if ($fileTmpPath == null) $fileTmpPath = $this->filesTmp;

        $nameCharacteristic = [];
        foreach ($characts as $c)
            if ($c->characteristicObjectWork->value_type == 6)
                $nameCharacteristic[] = $c->characteristicObjectWork->name;

        $saveFileNames = [];

        //$eId = ObjectEntryWork::find()->where(['material_object_id' => $this->id])->one()->entry_id;

        if ($fileTmpPath !== null)
        {
            for ($i = 0; $i < count($fileTmpPath); $i++)
            {
                $fileName = $_FILES['EntryWork']['name']["characteristics"][$i];
                if ($fileName == null) $fileName = $this->filesName[$i];


                if ($fileName !== "" && $fileName !== null)
                {
                    $fileNameCmps = explode(".", $fileName);
                    $fileExtension = strtolower(end($fileNameCmps));
                    $newFileName = substr($nameCharacteristic[$i], 0, 60).'_'.substr($this->name, 0, 30).'_'.$this->objEntrId .'.'.$fileExtension;
                    $uploadFileDir = Yii::$app->basePath.'/upload/files/material-object/characteristic/';
                    $dest_path = $uploadFileDir . $newFileName;

                    $saveFileNames[] = $newFileName;

                    move_uploaded_file($fileTmpPath[$i], $dest_path);

                    //$this->characteristics[] = $newFileName;
                }
                //else
                    //$this->characteristics[] = null;
            }
        }


        if ($this->characteristics !== null)
        {
            $counter = $_FILES['EntryWork']['name']["characteristics"];
            if ($counter == null) $counter = $this->filesName;


            if ($counter !== null)
            {
                $characts = KindCharacteristicWork::find()->joinWith(['characteristicObject characteristicObject'])->where(['kind_object_id' => $this->kindWork->id])->andWhere(['characteristicObject.value_type' => 6])->orderBy(['characteristic_object_id' => SORT_ASC])->all();


                for ($i = 0; $i < count($counter); $i++)
                {
                    $objChar = ObjectCharacteristicWork::find()->where(['material_object_id' => $this->id])->andWhere(['characteristic_object_id' => $characts[$i]->characteristicObjectWork->id])->one();


                    if ($objChar == null) $objChar = new ObjectCharacteristicWork();
                    
                    $objChar->document_value = $saveFileNames[$i];
                    $objChar->material_object_id = $this->id;
                    $objChar->characteristic_object_id = $characts[$i]->characteristicObjectWork->id;

                    if ($objChar->document_value !== NULL)
                        $objChar->save();
                }
            }
            
            $characts = KindCharacteristicWork::find()->joinWith(['characteristicObject characteristicObject'])->where(['kind_object_id' => $this->kindWork->id])->andWhere(['!=', 'characteristicObject.value_type', 6])->orderBy(['characteristic_object_id' => SORT_ASC])->all();

            for ($i = 0; $i < count($this->characteristics); $i++)
            {
                if ($this->characteristics[$i] !== null || strlen($this->characteristics[$i]) > 0)
                {
                    //$flag = false;
                    $objChar = ObjectCharacteristicWork::find()->where(['material_object_id' => $this->id])->andWhere(['characteristic_object_id' => $characts[$i]->characteristicObjectWork->id])->one();
                    
                    if ($objChar == null) $objChar = new ObjectCharacteristicWork();

                    if ($characts[$i]->characteristicObjectWork->value_type == 1)
                    {
                        $objChar->integer_value = $this->characteristics[$i];
                        //$flag = $objChar->integer_value == "";
                    }

                    if ($characts[$i]->characteristicObjectWork->value_type == 2)
                    {
                        $objChar->double_value = $this->characteristics[$i];
                        //$flag = $objChar->double_value == "";
                    }

                    if ($characts[$i]->characteristicObjectWork->value_type == 3)
                    {
                        $objChar->string_value = $this->characteristics[$i];
                        //$flag = $objChar->string_value == "";
                    }

                    if ($characts[$i]->characteristicObjectWork->value_type == 4)
                    {
                        $objChar->bool_value = $this->characteristics[$i];
                        //$flag = $objChar->bool_value == "";
                    }

                    if ($characts[$i]->characteristicObjectWork->value_type == 5)
                    {
                        $objChar->date_value = $this->characteristics[$i];
                        //$flag = $objChar->date_value == "";
                    }

                    if ($characts[$i]->characteristicObjectWork->value_type == 6)
                    {
                        $objChar->document_value = $saveFileNames[$i];
                        //$flag = $objChar->document_value == "";
                    }

                    if ($characts[$i]->characteristicObjectWork->value_type == 7)
                    {
                        $objChar->dropdown_value = $this->characteristics[$i];
                        //$flag = $objChar->document_value == "";
                    }

                    $objChar->material_object_id = $this->id;
                    $objChar->characteristic_object_id = $characts[$i]->characteristicObjectWork->id;

                    //if (!$flag)

                    $objChar->save();

                }
            }
        }

        // тут должны работать проверки на ошибки
        $errorsCheck = new MaterialObjectErrorsWork();
        $errorsCheck->CheckErrorsMaterialObjectWithoutAmnesty($this->id);
    }

    private function IsNullCharacterstic($characteristic)
    {
        return $characteristic->integer_value == null && $characteristic->double_value == null && $characteristic->string_value == null &&
            $characteristic->bool_value == null && $characteristic->date_value == null && $characteristic->document_value == null;
    }

    public function getErrorsWork()
    {
        $errorsList = MaterialObjectErrorsWork::find()->where(['material_object_id' => $this->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
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

    public function beforeDelete()
    {
        $objChar = ObjectCharacteristicWork::find()->where(['material_object_id' => $this->id])->all();

        foreach ($objChar as $one)
            $one->delete();

        $subs = MaterialObjectSubobjectWork::find()->where(['material_object_id' => $this->id])->all();
        $sId = [];
        foreach ($subs as $one)
            $sId[] = $one->subobject_id;

        $realSubs = SubobjectWork::find()->where(['IN', 'id', $sId])->all();

        foreach ($subs as $one)
            $one->delete();

        foreach ($realSubs as $one)
            $one->delete();

        $errors = MaterialObjectErrorsWork::find()->where(['material_object_id' => $this->id])->all();
        foreach ($errors as $error) $error->delete();

        return parent::beforeDelete();
    }

}
