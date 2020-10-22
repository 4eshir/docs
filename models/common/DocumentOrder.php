<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "document_order".
 *
 * @property int $id
 * @property int $order_number
 * @property string $order_name
 * @property string $order_date
 * @property int $signed_id
 * @property int $bring_id
 * @property int $executor_id
 * @property int $scan
 * @property int $register_id
 *
 * @property People $bring
 * @property People $executor
 * @property People $register
 * @property People $signed
 * @property Responsible[] $responsibles
 */
class DocumentOrder extends \yii\db\ActiveRecord
{
    public $scanFile;
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
            [['scanFile'], 'file', 'extensions' => 'txt, png', 'skipOnEmpty' => false],

            [['order_number', 'order_name', 'order_date', 'signed_id', 'bring_id', 'executor_id', 'scan', 'register_id'], 'required'],
            [['order_number', 'signed_id', 'bring_id', 'executor_id', 'scan', 'register_id'], 'integer'],
            [['order_date'], 'safe'],
            [['order_name'], 'string', 'max' => 1000],
            [['order_number'], 'unique'],
            [['bring_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['bring_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['executor_id' => 'id']],
            [['register_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['register_id' => 'id']],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['signed_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_number' => 'Order Number',
            'order_name' => 'Order Name',
            'order_date' => 'Order Date',
            'signed_id' => 'Signed ID',
            'bring_id' => 'Bring ID',
            'executor_id' => 'Executor ID',
            'scan' => 'Scan',
            'register_id' => 'Register ID',
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
     * Gets query for [[Register]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegister()
    {
        return $this->hasOne(People::className(), ['id' => 'register_id']);
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
     * Gets query for [[Responsibles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsibles()
    {
        return $this->hasMany(Responsible::className(), ['document_order_id' => 'id']);
    }
}
