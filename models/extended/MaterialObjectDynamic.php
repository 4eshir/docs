<?php


namespace app\models\extended;
use app\models\common\MaterialObject;
use app\models\work\MaterialObjectWork;
use app\models\work\FinanceSourceWork;
use yii\base\Model;

class MaterialObjectDynamic extends Model
{
    public $name;
    public $photo_local;
    public $photo_cloud;
    public $count;
    public $price;
    public $number;
    public $attribute;
    public $finance_source_id;
    public $inventory_number;
    public $type;
    public $kind_id;
    public $is_education;
    public $state;
    public $damage;
    public $status;
    public $write_off;
    public $lifetime;
    public $expiration_date;
    public $create_date;

    public $photoFile; //поле для загрузки фотографии объекта
    public $expirationDate; //дата окончания срока годности
    public $characteristics; //список характеристик объекта

    public $amount; //количество объектов в записи накладной

    public function rules()
    {
        return [
            //[['name', 'price', 'number', 'finance_source_id', 'type', 'is_education'], 'required'],
            [['count', 'number', 'finance_source_id', 'type', 'is_education', 'state', 'status', 'write_off', 'expiration_date', 'kind_id', 'amount'], 'integer'],
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
            'status' => 'Объект в работоспособном состоянии',
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

}