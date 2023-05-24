<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "backup_visit".
 *
 * @property int $id
 * @property int $foreign_event_participant_id
 * @property int $training_group_lesson_id
 * @property int $status
 *
 * @property ForeignEventParticipants $foreignEventParticipant
 */
class BackupVisit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'backup_visit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['foreign_event_participant_id', 'training_group_lesson_id'], 'required'],
            [['foreign_event_participant_id', 'training_group_lesson_id', 'status'], 'integer'],
            [['foreign_event_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::className(), 'targetAttribute' => ['foreign_event_participant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'foreign_event_participant_id' => 'Foreign Event Participant ID',
            'training_group_lesson_id' => 'Training Group Lesson ID',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[ForeignEventParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEventParticipant()
    {
        return $this->hasOne(ForeignEventParticipants::className(), ['id' => 'foreign_event_participant_id']);
    }
}
