<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "visit".
 *
 * @property int $id
 * @property int $foreign_event_participant_id
 * @property int $training_group_lesson_id
 * @property int $status
 *
 * @property ForeignEventParticipants $foreignEventParticipant
 * @property TrainingGroupLesson $trainingGroupLesson
 */
class Visit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'visit';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['foreign_event_participant', 'training_group_lesson_id'], 'required'],
            [['foreign_event_participant_id', 'training_group_lesson_id', 'status'], 'integer'],
            [['foreign_event_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::className(), 'targetAttribute' => ['foreign_event_participants' => 'id']],
            [['training_group_lesson_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupLesson::className(), 'targetAttribute' => ['training_group_lesson_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'foreign_event_participant_id' => 'Foreign Event Participants',
            'training_group_lesson_id' => 'Training Group Lesson ID',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[ForeignEventParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEventParticipant()
    {
        return $this->hasOne(ForeignEventParticipants::className(), ['id' => 'foreign_event_participant_id']);
    }

    /**
     * Gets query for [[TrainingGroupLesson]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupLesson()
    {
        return $this->hasOne(TrainingGroupLesson::className(), ['id' => 'training_group_lesson_id']);
    }
    
}
