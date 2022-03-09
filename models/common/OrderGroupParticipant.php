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
 * @property int|null $link_id
 *
 * @property OrderGroup $orderGroup
 * @property TrainingGroupParticipant $groupParticipant
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
            [['order_group_id', 'group_participant_id', 'status', 'link_id'], 'integer'],
            [['order_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => OrderGroup::className(), 'targetAttribute' => ['order_group_id' => 'id']],
            [['group_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupParticipant::className(), 'targetAttribute' => ['group_participant_id' => 'id']],
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
            'link_id' => 'Link ID',
        ];
    }

    /**
     * Gets query for [[OrderGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderGroup()
    {
        return $this->hasOne(OrderGroup::className(), ['id' => 'order_group_id']);
    }

    /**
     * Gets query for [[GroupParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroupParticipant()
    {
        return $this->hasOne(TrainingGroupParticipant::className(), ['id' => 'group_participant_id']);
    }
}
