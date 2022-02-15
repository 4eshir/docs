<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "order_group_participant".
 *
 * @property int $id
 * @property int $order_group_id
 * @property int $group_participant_id
 * @property int $status
 */
class OrderGroupParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order_group_participant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_group_id', 'group_participant_id', 'status'], 'required'],
            [['order_group_id', 'group_participant_id', 'status'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_group_id' => 'Order Group ID',
            'group_participant_id' => 'Group Participant ID',
            'status' => 'Status',
        ];
    }
}
