<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "material_object".
 *
 * @property int $id
 * @property string $name
 * @property string|null $photo_local
 * @property string|null $photo_cloud
 * @property int|null $count
 * @property float $price стоимость за одну штуку
 * @property string $attribute ОС или ТМЦ
 * @property int $finance_source_id источник финансирования
 * @property string|null $inventory_number
 * @property int $type тип - расходуемый или нет
 * @property int|null $kind_id
 * @property int $is_education учебно-материально-технический ресурс или нет
 * @property int|null $state % расходования
 * @property string|null $damage описание повреждений
 * @property int|null $status 1 - нерабочее
 2 - рабочее
 * @property int $write_off Статус списания: 0 - все ок, 1 - хочет списаться, 2 - списан
 * @property string|null $lifetime срок эксплуатации (для неорганики)
 * @property int|null $expiration_date срок годности
 * @property string|null $create_date дата производства товара
 * @property int $complex
 *
 * @property ContainerObject[] $containerObjects
 * @property LegacyMaterialResponsibility[] $legacyMaterialResponsibilities
 * @property FinanceSource $financeSource
 * @property KindObject $kind
 * @property MaterialObjectSubobject[] $materialObjectSubobjects
 * @property ObjectCharacteristic[] $objectCharacteristics
 * @property PeopleMaterialObject[] $peopleMaterialObjects
 * @property TemporaryJournal[] $temporaryJournals
 */
class MaterialObject extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'material_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'price', 'finance_source_id', 'type', 'is_education'], 'required'],
            [['count', 'type', 'kind_id', 'is_education', 'state', 'status', 'write_off', 'expiration_date', 'complex'], 'integer'],
            [['price'], 'number'],
            [['lifetime', 'create_date'], 'safe'],
            [['name', 'photo_local', 'photo_cloud'], 'string', 'max' => 1000],
            [['attribute'], 'string', 'max' => 3],
            [['inventory_number'], 'string', 'max' => 20],
            [['damage'], 'string', 'max' => 2000],
            [['finance_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => FinanceSource::className(), 'targetAttribute' => ['finance_source_id' => 'id']],
            [['kind_id'], 'exist', 'skipOnError' => true, 'targetClass' => KindObject::className(), 'targetAttribute' => ['kind_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'photo_local' => 'Photo Local',
            'photo_cloud' => 'Photo Cloud',
            'count' => 'Count',
            'price' => 'Price',
            'attribute' => 'Attribute',
            'finance_source_id' => 'Finance Source ID',
            'inventory_number' => 'Inventory Number',
            'type' => 'Type',
            'kind_id' => 'Kind ID',
            'is_education' => 'Is Education',
            'state' => 'State',
            'damage' => 'Damage',
            'status' => 'Status',
            'write_off' => 'Write Off',
            'lifetime' => 'Lifetime',
            'expiration_date' => 'Expiration Date',
            'create_date' => 'Create Date',
            'complex' => 'Complex',
        ];
    }

    /**
     * Gets query for [[ContainerObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContainerObjects()
    {
        return $this->hasMany(ContainerObject::className(), ['material_object_id' => 'id']);
    }

    /**
     * Gets query for [[LegacyMaterialResponsibilities]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLegacyMaterialResponsibilities()
    {
        return $this->hasMany(LegacyMaterialResponsibility::className(), ['material_object_id' => 'id']);
    }

    /**
     * Gets query for [[FinanceSource]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFinanceSource()
    {
        return $this->hasOne(FinanceSource::className(), ['id' => 'finance_source_id']);
    }

    /**
     * Gets query for [[Kind]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getKind()
    {
        return $this->hasOne(KindObject::className(), ['id' => 'kind_id']);
    }

    /**
     * Gets query for [[MaterialObjectSubobjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialObjectSubobjects()
    {
        return $this->hasMany(MaterialObjectSubobject::className(), ['material_object_id' => 'id']);
    }

    /**
     * Gets query for [[ObjectCharacteristics]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getObjectCharacteristics()
    {
        return $this->hasMany(ObjectCharacteristic::className(), ['material_object_id' => 'id']);
    }

    /**
     * Gets query for [[PeopleMaterialObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeopleMaterialObjects()
    {
        return $this->hasMany(PeopleMaterialObject::className(), ['material_object_id' => 'id']);
    }

    /**
     * Gets query for [[TemporaryJournals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemporaryJournals()
    {
        return $this->hasMany(TemporaryJournal::className(), ['material_object_id' => 'id']);
    }
}
