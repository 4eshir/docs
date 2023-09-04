<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "foreign_event_errors".
 *
 * @property int $id
 * @property int $foreign_event_id
 * @property int $errors_id
 * @property string $time_start
 * @property string|null $time_the_end
 * @property int|null $critical
 * @property int|null $amnesty
 *
 * @property ForeignEvent $foreignEvent
 * @property Errors $errors0
 */
class ForeignEventErrors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'foreign_event_errors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['foreign_event_id', 'errors_id', 'time_start'], 'required'],
            [['foreign_event_id', 'errors_id', 'critical', 'amnesty'], 'integer'],
            [['time_start', 'time_the_end'], 'safe'],
            [['foreign_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEvent::className(), 'targetAttribute' => ['foreign_event_id' => 'id']],
            [['errors_id'], 'exist', 'skipOnError' => true, 'targetClass' => Errors::className(), 'targetAttribute' => ['errors_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'foreign_event_id' => 'Foreign Event ID',
            'errors_id' => 'Errors ID',
            'time_start' => 'Time Start',
            'time_the_end' => 'Time The End',
            'critical' => 'Critical',
            'amnesty' => 'Amnesty',
        ];
    }

    /**
     * Gets query for [[ForeignEvent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEvent()
    {
        return $this->hasOne(ForeignEvent::className(), ['id' => 'foreign_event_id']);
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
}
