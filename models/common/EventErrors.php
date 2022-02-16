<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "event_errors".
 *
 * @property int $id
 * @property int $event_id
 * @property int $errors_id
 * @property string $time_start
 * @property string|null $time_the_end
 * @property int|null $critical
 * @property int|null $amnesty
 *
 * @property Errors $errors0
 * @property Event $event
 */
class EventErrors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event_errors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['event_id', 'errors_id', 'time_start'], 'required'],
            [['event_id', 'errors_id', 'critical', 'amnesty'], 'integer'],
            [['time_start', 'time_the_end'], 'safe'],
            [['errors_id'], 'exist', 'skipOnError' => true, 'targetClass' => Errors::className(), 'targetAttribute' => ['errors_id' => 'id']],
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
            'event_id' => 'Event ID',
            'errors_id' => 'Errors ID',
            'time_start' => 'Time Start',
            'time_the_end' => 'Time The End',
            'critical' => 'Critical',
            'amnesty' => 'Amnesty',
        ];
    }

    /**
     * Gets query for [[Errors0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getErrors0()
    {
        return $this->hasOne(Errors::className(), ['id' => 'errors_id']);
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
