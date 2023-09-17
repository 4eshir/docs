<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_participant_achievements_participant_achievement".
 *
 * @property int $id
 * @property int|null $teacher_participant_id
 * @property int|null $winner
 *
 * @property GetParticipantsTeacherParticipant $teacherParticipant
 */
class GetParticipantAchievementsParticipantAchievement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_participant_achievements_participant_achievement';
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
            [['teacher_participant_id', 'winner'], 'integer'],
            [['teacher_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => GetParticipantsTeacherParticipant::className(), 'targetAttribute' => ['teacher_participant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'teacher_participant_id' => 'Teacher Participant ID',
            'winner' => 'Winner',
        ];
    }

    /**
     * Gets query for [[TeacherParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherParticipant()
    {
        return $this->hasOne(GetParticipantsTeacherParticipant::className(), ['id' => 'teacher_participant_id']);
    }
}
