<?php

namespace app\models\work;

use app\models\common\Entry;
use app\models\common\ObjectEntry;
use app\models\work\InvoiceWork;
use Yii;


class EntryWork extends Entry
{
    public $name; //общее имя для всех объектов записи
    public $price; //общая цена для всех объектов
    public $create_date; //общая дата производства для всех объектов
    public $lifetime; //общая дата окончания эксплуатации для всех объектов
    public $expirationDate; //общая дата окончания срока годности для всех объектов
    public $inventory_number; //инвентарный номер (только для ОС)
    public $attribute; // ос или тмц
    public $complex; //составной объект или нет

<<<<<<< HEAD
=======
    public $kind_id;    // класс объекта
    public $object_id; // материальный объект для вывода характеристик класса

    public $characteristics; // массив характеристик класса
>>>>>>> afd4af68d2f6bd11cbe6fec6ade082a579c4df5e
    public $dynamic; //массив объектов и подобъектов

    public function rules()
    {
        return [
            [['name', 'create_date', 'lifetime', 'expirationDate'], 'string'],
            [['amount', 'price', 'complex'], 'integer'],
<<<<<<< HEAD
            [['inventory_number', 'dynamic'], 'safe'],
=======
            [['inventory_number', 'dynamic', 'characteristics'], 'safe'],
>>>>>>> afd4af68d2f6bd11cbe6fec6ade082a579c4df5e
            /*[['object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObjectWork::className(), 'targetAttribute' => ['object_id' => 'id']],*/
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Наименование объекта',
            'amount' => 'Количество',
            'price' => 'Цена за единицу',
            'number' => 'Номер товарной накладной',
            'create_date' => 'Дата производства объекта/ов',
            'expirationDate' => 'Дата окончания срока годности (при наличии)',
            'lifetime' => 'Дата окончания эксплуатации (опционально)',
            'inventory_number' => 'Инвентарный номер',
            'complex' => 'Составной объект',
<<<<<<< HEAD
=======
            'kind_id' => 'Класс объекта',
>>>>>>> afd4af68d2f6bd11cbe6fec6ade082a579c4df5e
        ];
    }

    public function fill()
    {
        $obj = ObjectEntryWork::find()->where(['entry_id' => $this->id])->orderBy(['id' => 'SORT_ASC'])->all();
           // MaterialObjectWork::find()->where(['id' => $this->object_id])->one();
        $this->name = $obj[0]->materialObject->name;
        $this->price = $obj[0]->materialObject->price;
        $this->create_date = $obj[0]->materialObject->create_date;
        $this->lifetime = $obj[0]->materialObject->lifetime;
        $this->expirationDate = $obj[0]->materialObjectWork->expirationDate;
        foreach ($obj as $object)
        {
            $this->inventory_number[] = $object->materialObject->inventory_number;
        }
        $this->attribute = $obj[0]->materialObject->attribute;
<<<<<<< HEAD
=======
        $this->complex = $obj[0]->materialObject->complex;
        $this->kind_id = $obj[0]->materialObject->kind_id;
        $this->object_id = $obj[0]->materialObject->id;
>>>>>>> afd4af68d2f6bd11cbe6fec6ade082a579c4df5e
    }

	public function getObjectWork()
    {
        return $this->hasOne(MaterialObjectWork::className(), ['entry_id' => 'id']);
    }

    public function getInvoiceEntriesWork()
    {
        return $this->hasMany(InvoiceEntryWork::className(), ['entry_id' => 'id']);
    }

    public function getInvoiceWork()
    {
        $invoice = $this->invoiceEntriesWork[0]->invoiceWork;
        return $invoice;
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub

        $parentSubobjectId = [];
        if ($this->dynamic !== null)
        {
            foreach ($this->dynamic as $obj)
            {
                $newObject = new SubobjectWork();
                $newObject->name = $obj["name"];
                $newObject->characteristics = $obj["text"];
                $newObject->state = $obj["state"];
                $newObject->entry_id = $this->id;
                $newObject->save();
                $parentSubobjectId[] = $newObject->id;
                if ($obj[0] !== null)
                {
                    $i = 0;
                    while ($obj[$i] !== null)
                    {
                        $newObject1 = new SubobjectWork();
                        $newObject1->name = $obj[$i]["name"];
                        $newObject1->characteristics = $obj[$i]["text"];
                        $newObject1->state = $obj[$i]["state"];
                        $newObject1->parent_id = $newObject->id;
                        $newObject1->save();
                        $i++;
                    }
                    
                }
            }
        }
        


        $objects = ObjectEntryWork::find()->where(['entry_id' => $this->id])->all();
        $i = 0;
        foreach ($objects as $object)
        {
<<<<<<< HEAD
            $object->materialObject->name = $this->name;
            $object->materialObject->price = $this->price;
            $object->materialObject->create_date = $this->create_date;
            $object->materialObject->lifetime = $this->lifetime;
            $object->materialObject->expiration_date = $this->expirationDate;
            $object->materialObject->inventory_number = $this->inventory_number[$i];
            $object->materialObject->complex = $this->complex;
            $object->materialObject->save();
=======
            $object->materialObjectWork->name = $this->name;
            $object->materialObjectWork->price = $this->price;
            $object->materialObjectWork->create_date = $this->create_date;
            $object->materialObjectWork->lifetime = $this->lifetime;
            $object->materialObjectWork->expiration_date = $this->expirationDate;
            $object->materialObjectWork->inventory_number = $this->inventory_number[$i];
            $object->materialObjectWork->complex = $this->complex;
            $object->materialObjectWork->characteristics = $this->characteristics;
            $object->materialObjectWork->save();
>>>>>>> afd4af68d2f6bd11cbe6fec6ade082a579c4df5e

            foreach ($parentSubobjectId as $one)
            {
                $objLink = new MaterialObjectSubobjectWork();
                $objLink->material_object_id = $object->materialObject->id;
                $objLink->subobject_id = $one;
                $objLink->save();
            }

            $i++;
        }
    }
}