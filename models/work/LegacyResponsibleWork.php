<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "legacy_responsible".
 *
 * @property int $id
 * @property int $people_id
 * @property int $responsibility_type_id
 * @property int|null $branch_id
 * @property int|null $auditorium_id
 * @property string $start_date
 * @property string|null $end_date
 * @property int|null $order_id
 *
 * @property DocumentOrder $order
 * @property People $people
 * @property ResponsibilityType $responsibilityType
 * @property Branch $branch
 * @property Auditorium $auditorium
 */
class LegacyResponsible extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'legacy_responsible';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['people_id', 'responsibility_type_id', 'start_date'], 'required'],
            [['people_id', 'responsibility_type_id', 'branch_id', 'auditorium_id', 'order_id'], 'integer'],
            [['start_date', 'end_date'], 'safe'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['people_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['people_id' => 'id']],
            [['responsibility_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResponsibilityType::className(), 'targetAttribute' => ['responsibility_type_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::className(), 'targetAttribute' => ['auditorium_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'people_id' => 'People ID',
            'responsibility_type_id' => 'Responsibility Type ID',
            'branch_id' => 'Branch ID',
            'auditorium_id' => 'Auditorium ID',
            'start_date' => 'Start Date',
            'end_date' => 'End Date',
            'order_id' => 'Order ID',
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(DocumentOrder::className(), ['id' => 'order_id']);
    }

    /**
     * Gets query for [[People]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasOne(People::className(), ['id' => 'people_id']);
    }

    /**
     * Gets query for [[ResponsibilityType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsibilityType()
    {
        return $this->hasOne(ResponsibilityType::className(), ['id' => 'responsibility_type_id']);
    }

    /**
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    /**
     * Gets query for [[Auditorium]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditorium()
    {
        return $this->hasOne(Auditorium::className(), ['id' => 'auditorium_id']);
    }
}
