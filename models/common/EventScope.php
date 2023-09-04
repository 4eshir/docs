<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "event_scope".
 *
 * @property int $id
 * @property int $event_id
 * @property int $participation_scope_id
 *
 * @property Event $event
 * @property ParticipationScope $participationScope
 */
class EventScope extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event_scope';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['event_id', 'participation_scope_id'], 'required'],
            [['event_id', 'participation_scope_id'], 'integer'],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']],
            [['participation_scope_id'], 'exist', 'skipOnError' => true, 'targetClass' => ParticipationScope::className(), 'targetAttribute' => ['participation_scope_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_id' => 'Event ID',
            'participation_scope_id' => 'Participation Scope ID',
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

    /**
     * Gets query for [[ParticipationScope]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipationScope()
    {
        return $this->hasOne(ParticipationScope::className(), ['id' => 'participation_scope_id']);
    }
}
