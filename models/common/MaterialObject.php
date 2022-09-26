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
 * @property int $count
 * @property float $price стоимость за одну штуку
 * @property int $number номер товарной накладной
 * @property string $attribute ОС или ТМЦ
 * @property int $finance_source_id источник финансирования
 * @property string|null $inventory_number
 * @property int $type тип - расходуемый или нет
 * @property int $is_education учебно-материально-технический ресурс или нет
 * @property int $state % расходования
 * @property string|null $damage описание повреждений
 * @property int|null $status рабочее или нет
 * @property int $write_off Статус списания: 0 - всё ок. 1 - хочет списаться. 2 - списан
 * @property string $lifetime срок эксплуатации (для неорганики)
 * @property int $expiration_date срок годности
 * @property string|null $create_date дата производства товара
 *
 * @property ComplexObject[] $complexObjects
 * @property Container[] $containers
 * @property ContainerObject[] $containerObjects
 * @property EventObject[] $eventObjects
 * @property HistoryObject[] $historyObjects
 * @property FinanceSource $financeSource
 * @property TemporaryObjectJournal[] $temporaryObjectJournals
 * @property TrainingGroupObject[] $trainingGroupObjects
 * @property UnionObject[] $unionObjects
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
            [['name', 'count', 'price', 'number', 'finance_source_id', 'type', 'is_education'], 'required'],
            [['count', 'number', 'finance_source_id', 'type', 'is_education', 'state', 'status', 'write_off', 'expiration_date'], 'integer'],
            [['price'], 'number'],
            [['lifetime', 'create_date'], 'safe'],
            [['name', 'photo_local', 'photo_cloud'], 'string', 'max' => 1000],
            [['attribute'], 'string', 'max' => 3],
            [['inventory_number'], 'string', 'max' => 20],
            [['damage'], 'string', 'max' => 2000],
            [['finance_source_id'], 'exist', 'skipOnError' => true, 'targetClass' => FinanceSource::className(), 'targetAttribute' => ['finance_source_id' => 'id']],
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
            'number' => 'Number',
            'attribute' => 'Attribute',
            'finance_source_id' => 'Finance Source ID',
            'inventory_number' => 'Inventory Number',
            'type' => 'Type',
            'is_education' => 'Is Education',
            'state' => 'State',
            'damage' => 'Damage',
            'status' => 'Status',
            'write_off' => 'Write Off',
            'lifetime' => 'Lifetime',
            'expiration_date' => 'Expiration Date',
            'create_date' => 'Create Date',
        ];
    }

    /**
     * Gets query for [[ComplexObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getComplexObjects()
    {
        return $this->hasMany(ComplexObject::className(), ['material_object_id' => 'id']);
    }

    /**
     * Gets query for [[Containers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getContainers()
    {
        return $this->hasMany(Container::className(), ['material_object_id' => 'id']);
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
     * Gets query for [[EventObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventObjects()
    {
        return $this->hasMany(EventObject::className(), ['material_object_id' => 'id']);
    }

    /**
     * Gets query for [[HistoryObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getHistoryObjects()
    {
        return $this->hasMany(HistoryObject::className(), ['material_object_id' => 'id']);
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
     * Gets query for [[TemporaryObjectJournals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemporaryObjectJournals()
    {
        return $this->hasMany(TemporaryObjectJournal::className(), ['material_object_id' => 'id']);
    }

    /**
     * Gets query for [[TrainingGroupObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupObjects()
    {
        return $this->hasMany(TrainingGroupObject::className(), ['material_object_id' => 'id']);
    }

    /**
     * Gets query for [[UnionObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUnionObjects()
    {
        return $this->hasMany(UnionObject::className(), ['material_object_id' => 'id']);
    }
}
