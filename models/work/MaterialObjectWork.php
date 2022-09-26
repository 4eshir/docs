<?php

namespace app\models\work;

use app\models\common\MaterialObject;


class MaterialObjectWork extends MaterialObject
{
	public $photoFile; //поле для загрузки фотографии объекта
	public $expirationDate; //дата окончания срока годности

	public function rules()
    {
        return [
            [['name', 'count', 'price', 'number', 'finance_source_id', 'type', 'is_education'], 'required'],
            [['count', 'number', 'finance_source_id', 'type', 'is_education', 'state', 'status', 'write_off', 'expiration_date'], 'integer'],
            [['price'], 'number'],
            [['lifetime', 'create_date'], 'safe'],
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
            'inventory_number' => 'Инвентарный номер',
            'type' => 'Тип объекта',
            'is_education' => 'Является учебным материально-техническим ресурсом',
            'state' => 'Остаток (в %)',
            'damage' => 'Описание повреждений (опционально)',
            'status' => 'Состояние объекта',
            'write_off' => 'Статус списания',
            'lifetime' => 'Дата окончания эксплуатации',
            'expiration_date' => 'Срок годности (в днях)',
            'expirationDate' => 'Дата окончания срока годности',
            'create_date' => 'Дата производства объекта',
        ];
    }

    public function beforeSave($insert)
    {
    	$d1 = strtotime($this->expirationDate);
    	$d2 = strtotime($this->create_date);
    	$this->expiration_date = round(($d1 - $d2) / (60 * 60 * 24));
    	return parent::beforeSave($insert);
    }

}
