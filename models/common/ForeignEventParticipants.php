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
            [['firstname', 'secondname', 'patronymic'], 'required'],
            [['firstname', 'secondname', 'patronymic', 'birthdate'], 'string'],
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

    public function getFullName()
    {
        return $this->secondname.' '.$this->firstname.' '.$this->patronymic;
    }

    public function getShortName()
    {
        return $this->secondname.' '.mb_substr($this->firstname, 0, 1).'.'.mb_substr($this->patronymic, 0, 1).'.';
    }

    public function getDocuments()
    {
        $docs = ParticipantFiles::find()->where(['participant_id' => $this->id])->all();
        $docsLink = '';
        foreach ($docs as $docOne)
        {
            $docsLink = $docsLink.Html::a($docOne->filename, \yii\helpers\Url::to(['foreign-event/get-file', 'fileName' => $docOne->filename, 'type' => 'participants'])).'<br>';
        }
        return $docsLink;
    }

    public function getAchievements()
    {
        $achieves = ParticipantAchievement::find()->where(['participant_id' => $this->id])->all();
        $achievesLink = '';
        foreach ($achieves as $achieveOne)
        {
            $achievesLink = $achievesLink.$achieveOne->achievment.' &mdash; '.Html::a($achieveOne->foreignEvent->name, \yii\helpers\Url::to(['foreign-event/view', 'id' => $achieveOne->foreign_event_id])).
                ' ('.$achieveOne->foreignEvent->start_date.')'.'<br>';
        }
        return $achievesLink;
    }
}
