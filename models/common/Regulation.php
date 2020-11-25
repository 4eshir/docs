<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "regulation".
 *
 * @property int $id
 * @property string $date
 * @property string $name
 * @property int $order_id
 * @property int $ped_council_number
 * @property string $ped_council_date
 * @property int $par_council_number
 * @property string $par_council_date
 * @property int $state
 * @property string $scan
 *
 * @property Expire[] $expires
 * @property Expire[] $expires0
 * @property DocumentOrder $order
 */
class Regulation extends \yii\db\ActiveRecord
{
    public $expireOrder;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'regulation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['date', 'name', 'order_id', 'ped_council_number', 'ped_council_date', 'par_council_number', 'par_council_date', 'state', 'scan'], 'required'],
            [['date', 'ped_council_date', 'par_council_date'], 'safe'],
            [['order_id', 'ped_council_number', 'par_council_number', 'state'], 'integer'],
            [['name', 'scan'], 'string', 'max' => 1000],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'date' => 'Дата',
            'name' => 'Тема положения',
            'order_id' => 'Order ID',
            'ped_council_number' => '№ педагогического совета',
            'ped_council_date' => 'Дата педагогического совета',
            'par_council_number' => '№ родителького совета',
            'par_council_date' => 'Дата родительского совета',
            'state' => 'Состояние',
            'scan' => 'Скан',
        ];
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
        return $this->hasMany(Expire::className(), ['expire_regulation_id' => 'id']);
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
}
