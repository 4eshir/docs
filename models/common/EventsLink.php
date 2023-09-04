<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "events_link".
 *
 * @property int $id
 * @property int $event_external_id
 * @property int $event_id
 */
class EventsLink extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'events_link';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['event_external_id', 'event_id'], 'required'],
            [['event_external_id', 'event_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'event_external_id' => 'Event External ID',
            'event_id' => 'Event ID',
        ];
    }

    /**
     * Gets query for [[EventExternal]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventExternal()
    {
        return $this->hasOne(EventExternal::className(), ['id' => 'event_external_id']);
    }
}
