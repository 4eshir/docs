<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_participants_teacher_participant".
 *
 * @property int $id
 * @property int|null $participant_id
 * @property int|null $teacher_id
 * @property int|null $teacher2_id
 * @property int|null $foreign_event_id
 * @property int|null $focus
 * @property int|null $allow_remote_id
 *
 * @property GetParticipantsTeacherParticipantBranch[] $getParticipantsTeacherParticipantBranches
 * @property GetParticipantsTeam[] $getParticipantsTeams
 */
class GetParticipantsTeacherParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_participants_teacher_participant';
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
            [['participant_id', 'teacher_id', 'teacher2_id', 'foreign_event_id', 'focus', 'allow_remote_id'], 'integer'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'participant_id' => 'Participant ID',
            'teacher_id' => 'Teacher ID',
            'teacher2_id' => 'Teacher 2 ID',
            'foreign_event_id' => 'Foreign Event ID',
            'focus' => 'Focus',
            'allow_remote_id' => 'Allow Remote ID',
        ];
    }

    /**
     * Gets query for [[GetParticipantsTeacherParticipantBranches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGetParticipantsTeacherParticipantBranches()
    {
        return $this->hasMany(GetParticipantsTeacherParticipantBranch::className(), ['teacher_participant_id' => 'id']);
    }

    /**
     * Gets query for [[GetParticipantsTeams]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGetParticipantsTeams()
    {
        return $this->hasMany(GetParticipantsTeam::className(), ['teacher_participant_id' => 'id']);
    }
}
