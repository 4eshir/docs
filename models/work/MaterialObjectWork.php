<?php

namespace app\models\work;

use app\models\common\MaterialObject;
use app\models\work\ObjectCharacteristicWork;


class MaterialObjectWork extends MaterialObject
{
	public $photoFile; //поле для загрузки фотографии объекта
	public $expirationDate; //дата окончания срока годности
    public $characteristics; //список характеристик объекта

	public function rules()
    {
        return [
            [['name', 'count', 'price', 'number', 'finance_source_id', 'type', 'is_education'], 'required'],
            [['count', 'number', 'finance_source_id', 'type', 'is_education', 'state', 'status', 'write_off', 'expiration_date', 'kind_id'], 'integer'],
            [['price'], 'number'],
            [['lifetime', 'create_date', 'characteristics'], 'safe'],
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
            'price' => 'Цена за единицу',
            'number' => 'Номер товарной накладной',
            'attribute' => 'Признак',
            'finance_source_id' => 'Источник финансирования',
            'financeSourceString' => 'Источник финансирования',
            'inventory_number' => 'Инвентарный номер',
            'type' => 'Тип объекта',
            'typeString' => 'Тип объекта',
            'is_education' => 'Является учебным материально-техническим ресурсом',
            'isEducationString' => 'Является учебным материально-техническим ресурсом',
            'state' => 'Остаток (в %)',
            'damage' => 'Описание повреждений (опционально)',
            'status' => 'Состояние объекта',
            'statusString' => 'Объект в работоспособном состоянии',
            'write_off' => 'Статус списания',
            'writeOffString' => 'Статус списания',
            'lifetime' => 'Дата окончания эксплуатации',
            'expiration_date' => 'Срок годности (в днях)',
            'expirationDate' => 'Дата окончания срока годности',
            'create_date' => 'Дата производства объекта',
            'kind_id' => 'Вид объекта',
            'kindString' => 'Вид объекта',
        ];
    }

    public function getKindWork()
    {
        return $this->hasOne(KindObjectWork::className(), ['id' => 'kind_id']);
    }

    public function getFinanceSourceWork()
    {
        return $this->hasOne(FinanceSourceWork::className(), ['id' => 'finance_source_id']);
    }

    public function getKindString()
    {
        $chars = ObjectCharacteristicWork::find()->where(['material_object_id' => $this->id])->orderBy(['characteristic_object_id' => SORT_ASC])->all();
        $res = '<table>';
        
        foreach ($chars as $char)
        {
            $res .= '<tr><td style="padding-right: 15px; padding-bottom: 2px">'.$char->characteristicObjectWork->name.'</td><td>'.$char->getValue().'</td>';
        }
        $res .= '</table>';
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

    public function getWriteOffString()
    {
        if ($this->write_off == 0)
            return 'Доступен для эксплуатации';
        return $this->write_off == 1 ? 'Готов к списанию' : 'Списан';
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
                    {
                        $objChar->integer_value = $this->characteristics[$i];
                        $objChar->double_value = null;
                        $objChar->string_value = null;
                    }
                    if ($characts[$i]->characteristicObjectWork->value_type == 2)
                    {
                        $objChar->integer_value = null;
                        $objChar->double_value = $this->characteristics[$i];
                        $objChar->string_value = null;
                    }
                    if ($characts[$i]->characteristicObjectWork->value_type == 3)
                    {
                        $objChar->integer_value = null;
                        $objChar->double_value = null;
                        $objChar->string_value = $this->characteristics[$i];
                    }

                    $objChar->material_object_id = $this->id;
                    $objChar->characteristic_object_id = $characts[$i]->id;
                    $objChar->save();
                }
            }
        }
    }


}
