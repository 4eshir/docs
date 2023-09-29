<?php

namespace app\models\common;

use app\models\components\FileWizard;
use Yii;

/**
 * This is the model class for table "regulation".
 *
 * @property int $id
 * @property string $date
 * @property string $name
 * @property string $short_name
 * @property int $order_id
 * @property int $ped_council_number
 * @property string $ped_council_date
 * @property int $par_council_number
 * @property string $par_council_date
 * @property int regulation_type_id
 * @property int $state
 * @property string $scan
 * @property int $creator_id
 * @property int $last_edit_id
 *
 * @property Expire[] $expires
 * @property Expire[] $expires0
 * @property DocumentOrder $order
 * @property User $creator
 * @property User $lastEdit
 */
class Regulation extends \yii\db\ActiveRecord
{

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
            [['date', 'name', 'order_id', 'state'], 'required'],
            [['date', 'ped_council_date', 'par_council_date'], 'safe'],
            [['order_id', 'ped_council_number', 'par_council_number', 'state', 'regulation_type_id', 'creator_id', 'last_edit_id'], 'integer'],
            [['name', 'scan', 'short_name'], 'string', 'max' => 1000],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['last_edit_id' => 'id']],
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
            'name' => 'Наименование положения',
            'short_name' => 'Краткое наименование положения',
            'order_id' => 'Приказ',
            'ped_council_number' => '№ педагогического совета',
            'ped_council_date' => 'Дата педагогического совета',
            'par_council_number' => '№ совета родителей',
            'par_council_date' => 'Дата совета родителей',
            'state' => 'Состояние',
            'scan' => 'Скан',
        ];
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

    /**
     * Gets query for [[RegulationType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegulationType()
    {
        return $this->hasOne(RegulationType::className(), ['id' => 'regulation_type_id']);
    }

    //--------------------------

    public function getFullName()
    {
        $order_num = '';
        if ($this->order->order_postfix !== null)
            $order_num = $this->order->order_number.'/'.$this->order->order_copy_id.'/'.$this->order->order_postfix.' '.$this->order->order_name;
        else
            $order_num = $this->order->order_number.'/'.$this->order->order_copy_id.' '.$this->order->order_name;
        return 'Приказ  "'.$order_num.'"';
    }

}
