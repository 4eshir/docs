<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "foreign_event_participants".
 *
 * @property int $id
 * @property string $firstname
 * @property string $secondname
 * @property string|null $patronymic
 * @property string $birthdate
 * @property string $sex
 * @property string|null $email
 * @property int $is_true
 * @property int|null $guaranted_true
 *
 * @property ParticipantAchievement[] $participantAchievements
 * @property ParticipantFiles[] $participantFiles
 * @property ParticipantForeignEvent[] $participantForeignEvents
 * @property PersonalDataForeignEventParticipant[] $personalDataForeignEventParticipants
 * @property TeacherParticipant[] $teacherParticipants
 * @property Team[] $teams
 * @property TrainingGroupParticipant[] $trainingGroupParticipants
 * @property Visit[] $visits
 */
class ForeignEventParticipants extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'foreign_event_participants';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['firstname', 'secondname', 'birthdate'], 'required'],
            [['birthdate'], 'safe'],
            [['is_true', 'guaranted_true'], 'integer'],
            [['firstname', 'secondname', 'patronymic'], 'string', 'max' => 1000],
            [['sex', 'email'], 'string', 'max' => 100],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Firstname',
            'secondname' => 'Secondname',
            'patronymic' => 'Patronymic',
            'birthdate' => 'Birthdate',
            'sex' => 'Sex',
            'email' => 'Email',
            'is_true' => 'Is True',
            'guaranted_true' => 'Guaranted True',
        ];
    }

    /**
     * Gets query for [[ParticipantAchievements]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipantAchievements()
    {
        return $this->hasMany(ParticipantAchievement::className(), ['participant_id' => 'id']);
    }

    /**
     * Gets query for [[ParticipantFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipantFiles()
    {
        return $this->hasMany(ParticipantFiles::className(), ['participant_id' => 'id']);
    }

    /**
     * Gets query for [[ParticipantForeignEvents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipantForeignEvents()
    {
        return $this->hasMany(ParticipantForeignEvent::className(), ['participant_id' => 'id']);
    }

    /**
     * Gets query for [[PersonalDataForeignEventParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonalDataForeignEventParticipants()
    {
        return $this->hasMany(PersonalDataForeignEventParticipant::className(), ['foreign_event_participant_id' => 'id']);
    }

    /**
     * Gets query for [[TeacherParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherParticipants()
    {
        return $this->hasMany(TeacherParticipant::className(), ['participant_id' => 'id']);
    }

    /**
     * Gets query for [[Teams]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeams()
    {
        return $this->hasMany(Team::className(), ['participant_id' => 'id']);
    }

    /**
     * Gets query for [[TrainingGroupParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupParticipants()
    {
        return $this->hasMany(TrainingGroupParticipant::className(), ['participant_id' => 'id']);
    }

    /**
     * Gets query for [[Visits]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getVisits()
    {
        return $this->hasMany(Visit::className(), ['foreign_event_participant_id' => 'id']);
    }
}
