<?php

namespace app\models\common;

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
 *
 * @property Expire[] $expires
 * @property Expire[] $expires0
 * @property DocumentOrder $order
 */
class Regulation extends \yii\db\ActiveRecord
{
    public $expires;
    public $scanFile;
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
            [['order_id', 'ped_council_number', 'par_council_number', 'state', 'regulation_type_id'], 'integer'],
            [['name', 'scan', 'short_name'], 'string', 'max' => 1000],
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

    public function uploadScanFile()
    {
        $path = '@app/upload/files/regulation/';
        $date = $this->date;
        $new_date = '';
        for ($i = 0; $i < strlen($date); ++$i)
            if ($date[$i] != '-')
                $new_date = $new_date.$date[$i];
        $filename = '';
        if ($this->short_name == null)
            $filename = $new_date.'_'.$this->name;
        else
            $filename = $new_date.'_'.$this->short_name;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);

        $this->scan = $res . '.' . $this->scanFile->extension;
        $this->scanFile->saveAs( $path . $res . '.' . $this->scanFile->extension);
    }

    public function checkForeignKeys()
    {
        $orders = DocumentOrder::find()->where(['id' => $this->order_id])->all();
        $events = Event::find()->where(['regulation_id' => $this->id])->all();
        if (count($orders) > 0 || count($events) > 0)
            return true;
        else
            return false;
    }

    public static function CheckRegulationState($orderId, $state = null)
    {
        $regs = Regulation::find()->where(['order_id' => $orderId])->all();
        if ($state == null)
            $state = DocumentOrder::find()->where(['id' => $orderId])->one()->state;
        foreach ($regs as $regOne)
        {
            $regOne->state = $state;
            $regOne->save(false);
        }
    }

    //--------------------------

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        $expireOrder = [new Expire];
        $expireOrder = $this->expires;
        if ($expireOrder !== null && strlen($expireOrder[0]->expire_regulation_id) != 0)
        {
            for ($i = 0; $i < count($expireOrder); $i++)
            {
                $reg = Regulation::find()->where(['order_id' => $expireOrder[$i]])->one();
                if ($reg !== null)
                {
                    $reg->state = 'Утратило силу';
                    $reg->save(false);
                }

                $expireOrder[$i]->active_regulation_id = $this->order_id;
                $expireOrder[$i]->save(false);
            }
        }

    }

    public function beforeDelete()
    {
        $expires = Expire::find()->where(['expire_regulation_id' => $this->id])->all();
        foreach ($expires as $expire)
            $expire->delete();
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }
}
