<?php

namespace app\models\work;

use app\models\common\MaterialObject;
use app\models\work\ObjectCharacteristicWork;
use yii\helpers\Html;


class MaterialObjectWork extends MaterialObject
{

	public $photoFile; //поле для загрузки фотографии объекта
	public $expirationDate; //дата окончания срока годности
    public $characteristics; //список характеристик объекта

    public $amount; //количество объектов в записи накладной

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
            [['count', 'finance_source_id', 'type', 'is_education', 'state', 'status', 'write_off', 'expiration_date', 'kind_id', 'amount', 'complex'], 'integer'],
            [['price'], 'number'],
            [['lifetime', 'create_date', 'characteristics', 'name', 'price', 'number', 'finance_source_id', 'type', 'is_education'], 'safe'],
            [['name', 'photo_local', 'photo_cloud', 'expirationDate'], 'string', 'max' => 1000],
            [['attribute'], 'string', 'max' => 3],
            [['inventory_number'], 'string', 'max' => 20],
            [['damage'], 'string', 'max' => 2000],
            [['finance_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => FinanceSourceWork::className(), 'targetAttribute' => ['finance_source_id' => 'id']],
            [['photoFile'], 'file', 'extensions' => 'jpg, jpeg, png, pdf, webp, jfif', 'skipOnEmpty' => true, 'maxSize' => 104857600]
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование объекта',
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
        ];
    }

    public function getKindWork()
    {
        return $this->hasOne(KindObjectWork::className(), ['id' => 'kind_id']);
    }

    public function getObjectEntryWork()
    {
        return $this->hasOne(ObjectEntryWork::className(), ['material_object_id' => 'id']);
    }

    public function getFinanceSourceWork()
    {
        return $this->hasOne(FinanceSourceWork::className(), ['id' => 'finance_source_id']);
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

    public function getNumberLink()
    {
        $entry = ObjectEntryWork::find()->where(['material_object_id' => $this->id])->one();
        $invoice = InvoiceEntryWork::find()->where(['entry_id' => $entry->entry_id])->one();

        $type = $invoice->invoiceWork->type;
        $name = ['Накладная', 'Акт', 'УПД', 'Протокол'];

        $fullName = $name[$type] . ' №' . $invoice->invoiceWork->number;

        return Html::a($fullName, \yii\helpers\Url::to(['invoice/view', 'id' => $invoice->invoiceWork->id]));
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

    public function afterSave($insert, $changedAttributes)
    {
        $characts = KindCharacteristicWork::find()->where(['kind_object_id' => $this->kindWork->id])->orderBy(['characteristic_object_id' => SORT_ASC])->all();

        if ($this->characteristics !== null)
        {
            $objChar = ObjectCharacteristicWork::find()->where(['material_object_id' => $this->id])->all();
            foreach ($objChar as $c) $c->delete();

            for ($i = 0; $i < count($this->characteristics); $i++)
            {

                if ($this->characteristics[$i] !== null || strlen($this->characteristics[$i]) > 0)
                {
                    $objChar = new ObjectCharacteristicWork();

                    if ($characts[$i]->characteristicObjectWork->value_type == 1)
                        $objChar->integer_value = $this->characteristics[$i];

                    if ($characts[$i]->characteristicObjectWork->value_type == 2)
                        $objChar->double_value = $this->characteristics[$i];

                    if ($characts[$i]->characteristicObjectWork->value_type == 3)
                        $objChar->string_value = $this->characteristics[$i];

                    if ($characts[$i]->characteristicObjectWork->value_type == 4)
                    {
                        if ($this->characteristics[$i] == 2)
                            $objChar->bool_value = 0;
                        else
                            $objChar->bool_value = $this->characteristics[$i];
                    }

                    if ($characts[$i]->characteristicObjectWork->value_type == 5)
                        $objChar->date_value = $this->characteristics[$i];

                    $objChar->material_object_id = $this->id;
                    $objChar->characteristic_object_id = $characts[$i]->characteristicObjectWork->id;

                    $objChar->save();
                }
            }
        }
    }


}
