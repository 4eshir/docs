<?php

namespace app\models\common;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "foreign_event_participants".
 *
 * @property int $id
 * @property string $firstname
 * @property string $secondname
 * @property string $patronymic
 * @property string $birthdate
 * @property string $sex
 * @property int $is_true
 * @property int $guaranted_true
 *
 * @property ParticipantAchievement[] $participantAchievements
 * @property ParticipantFiles[] $participantFiles
 * @property ParticipantForeignEvent[] $participantForeignEvents
 * @property TeacherParticipant[] $teacherParticipants
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
            [['firstname', 'secondname', 'sex'], 'required'],
            [['is_true', 'guaranted_true'], 'integer'],
            [['firstname', 'secondname', 'patronymic', 'birthdate', 'sex'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Имя',
            'secondname' => 'Фамилия',
            'patronymic' => 'Отчество',
            'documents' => 'Заявки',
            'achievements' => 'Достижения',
            'birthdate' => 'Дата рождения',
            'sex' => 'Пол',
            'events' => 'Участие в мероприятиях',
            'studies' => 'Учебные группы участника',
            'is_true' => 'Данные корректны',
            'guaranted_true' => 'Данные корректны'
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
     * Gets query for [[TeacherParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherParticipants()
    {
        return $this->hasMany(TeacherParticipant::className(), ['participant_id' => 'id']);
    }

}
