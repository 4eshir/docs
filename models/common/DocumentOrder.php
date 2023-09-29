<?php

namespace app\models\common;

use app\models\null\PeopleNull;
use Yii;

/**
 * This is the model class for table "document_order".
 *
 * @property int $id
 * @property int $order_copy_id
 * @property string $order_number
 * @property int|null $order_postfix
 * @property string $order_name
 * @property string $order_date
 * @property int|null $signed_id
 * @property int|null $bring_id
 * @property int|null $executor_id
 * @property string $scan
 * @property string|null $doc
 * @property int $creator_id
 * @property int $last_edit_id
 * @property int|null $type
 * @property int|null $study_type
 * @property string|null $key_words
 * @property int|null $state
 * @property int $nomenclature_id
 *
 * @property People $bring
 * @property People $executor
 * @property People $creator
 * @property User $lastEdit
 * @property People $signed
 * @property Branch $nomenclature
 * @property Expire[] $expires
 * @property Expire[] $expires0
 * @property ForeignEvent[] $foreignEvents
 * @property ForeignEvent[] $foreignEvents0
 * @property LegacyResponsible[] $legacyResponsibles
 * @property OrderGroup[] $orderGroups
 * @property Regulation[] $regulations
 * @property Responsible[] $responsibles
 */
class DocumentOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_copy_id', 'order_number', 'order_name', 'order_date', 'scan', 'creator_id', 'last_edit_id'], 'required'],
            [['order_copy_id', 'order_postfix', 'signed_id', 'bring_id', 'executor_id', 'creator_id', 'last_edit_id', 'type', 'state', 'nomenclature_id'], 'integer'],
            [['order_date'], 'safe'],
            [['order_number'], 'string', 'max' => 100],
            [['order_name', 'scan', 'doc', 'key_words'], 'string', 'max' => 1000],
            [['bring_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['bring_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['executor_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['last_edit_id' => 'id']],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['signed_id' => 'id']],
            [['nomenclature_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['nomenclature_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_copy_id' => 'Order Copy ID',
            'order_number' => 'Order Number',
            'order_postfix' => 'Order Postfix',
            'order_name' => 'Order Name',
            'order_date' => 'Order Date',
            'signed_id' => 'Signed ID',
            'bring_id' => 'Bring ID',
            'executor_id' => 'Executor ID',
            'scan' => 'Scan',
            'doc' => 'Doc',
            'creator_id' => 'Register ID',
            'type' => 'Type',
            'key_words' => 'Key Words',
            'state' => 'State',
            'nomenclature_id' => 'Nomenclature ID',
        ];
    }

    /**
     * Gets query for [[Bring]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBring()
    {
        return $this->hasOne(People::className(), ['id' => 'bring_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(People::className(), ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[Signed]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSigned()
    {
        return $this->hasOne(People::className(), ['id' => 'signed_id']);
    }

    /**
     * Gets query for [[Nomenclature]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getNomenclature()
    {
        return $this->hasOne(Branch::className(), ['id' => 'nomenclature_id']);
    }

    /**
     * Gets query for [[Expires]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpires()
    {
        return $this->hasMany(Expire::className(), ['active_regulation_id' => 'id']);
    }

    /**
     * Gets query for [[Expires0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExpires0()
    {
        return $this->hasMany(Expire::className(), ['expire_order_id' => 'id']);
    }

    /**
     * Gets query for [[ForeignEvents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEvents()
    {
        return $this->hasMany(ForeignEvent::className(), ['order_participation_id' => 'id']);
    }

    /**
     * Gets query for [[ForeignEvents0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEvents0()
    {
        return $this->hasMany(ForeignEvent::className(), ['order_business_trip_id' => 'id']);
    }

    /**
     * Gets query for [[LegacyResponsibles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getLegacyResponsibles()
    {
        return $this->hasMany(LegacyResponsible::className(), ['order_id' => 'id']);
    }

    /**
     * Gets query for [[OrderGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderGroups()
    {
        return $this->hasMany(OrderGroup::className(), ['document_order_id' => 'id']);
    }

    /**
     * Gets query for [[Regulations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegulations()
    {
        return $this->hasMany(Regulation::className(), ['order_id' => 'id']);
    }

    /**
     * Gets query for [[Responsibles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsibles()
    {
        return $this->hasMany(Responsible::className(), ['document_order_id' => 'id']);
    }
}
