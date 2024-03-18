<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "visit".
 *
 * @property int $id
 * @property int|null $foreign_event_participant_id
 * @property int $training_group_lesson_id
 * @property int|null $training_group_participant_id
 * @property int $status
 *
 * @property TrainingGroupParticipant $trainingGroupParticipant
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
            [['id', 'foreign_event_participant_id', 'training_group_lesson_id'], 'required'],
            [['id', 'foreign_event_participant_id', 'training_group_lesson_id', 'training_group_participant_id', 'status'], 'integer'],
            [['id'], 'unique'],
            [['training_group_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupParticipant::className(), 'targetAttribute' => ['training_group_participant_id' => 'id']],
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
            'foreign_event_participant_id' => 'Foreign Event Participant ID',
            'training_group_lesson_id' => 'Training Group Lesson ID',
            'training_group_participant_id' => 'Training Group Participant ID',
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
     * Gets query for [[TrainingGroupParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupParticipant()
    {
        return $this->hasOne(TrainingGroupParticipant::className(), ['id' => 'training_group_participant_id']);
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
