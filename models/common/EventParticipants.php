<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "event_participants".
 *
 * @property int $id
 * @property int|null $child_participants
 * @property int|null $child_rst_participants
 * @property int|null $teacher_participants
 * @property int|null $other_participants
 * @property int|null $age_left_border
 * @property int|null $age_right_border
 * @property int $event_id
 *
 * @property Event $event
 */
class EventParticipants extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event_participants';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['child_participants', 'child_rst_participants', 'teacher_participants', 'other_participants', 'age_left_border', 'age_right_border', 'event_id'], 'integer'],
            [['event_id'], 'required'],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'child_participants' => 'Child Participants',
            'child_rst_participants' => 'Child RST Participants',
            'teacher_participants' => 'Teacher Participants',
            'other_participants' => 'Other Participants',
            'age_left_border' => 'Age Left Border',
            'age_right_border' => 'Age Right Border',
            'event_id' => 'Event ID',
        ];
    }

    /**
     * Gets query for [[Event]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Event::className(), ['id' => 'event_id']);
    }
}
