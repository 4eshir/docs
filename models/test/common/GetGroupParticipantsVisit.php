<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_group_participants_visit".
 *
 * @property int $id
 * @property int|null $foreign_event_participant_id
 * @property int|null $training_group_lesson_id
 * @property int|null $status
 *
 * @property GetGroupParticipantsForeignEventParticipant $foreignEventParticipant
 * @property GetGroupParticipantsTrainingGroupLesson $trainingGroupLesson
 */
class GetGroupParticipantsVisit extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_group_participants_visit';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_report_test');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['foreign_event_participant_id', 'training_group_lesson_id', 'status'], 'integer'],
            [['foreign_event_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => GetGroupParticipantsForeignEventParticipant::className(), 'targetAttribute' => ['foreign_event_participant_id' => 'id']],
            [['training_group_lesson_id'], 'exist', 'skipOnError' => true, 'targetClass' => GetGroupParticipantsTrainingGroupLesson::className(), 'targetAttribute' => ['training_group_lesson_id' => 'id']],
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
        return $this->hasOne(GetGroupParticipantsForeignEventParticipant::className(), ['id' => 'foreign_event_participant_id']);
    }

    /**
     * Gets query for [[TrainingGroupLesson]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupLesson()
    {
        return $this->hasOne(GetGroupParticipantsTrainingGroupLesson::className(), ['id' => 'training_group_lesson_id']);
    }
}
