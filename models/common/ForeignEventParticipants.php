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
            ['is_true', 'integer'],
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
            'is_true' => 'Данные корректны'
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

    public function getEvents()
    {
        $events = TeacherParticipant::find()->where(['participant_id' => $this->id])->all();
        $eventsLink = '';
        foreach ($events as $event)
            $eventsLink = $eventsLink.Html::a($event->foreignEvent->name, \yii\helpers\Url::to(['foreign-event/view', 'id' => $event->foreign_event_id])).'<br>';

        return $eventsLink;
    }

    public function getStudies()
    {
        $events = TrainingGroupParticipant::find()->where(['participant_id' => $this->id])->all();
        $eventsLink = '';
        foreach ($events as $event)
        {
            $eventsLink .= date('d.m.Y', strtotime($event->trainingGroup->start_date)).' - '.date('d.m.Y', strtotime($event->trainingGroup->finish_date)).' | ';
            $eventsLink = $eventsLink.Html::a('Группа '.$event->trainingGroup->number, \yii\helpers\Url::to(['training-group/view', 'id' => $event->training_group_id]));

            if ($event->trainingGroup->finish_date < date("Y-m-d"))
                $eventsLink .= ' (завершил обучение)';
            else
                $eventsLink .= ' <div style="background-color: green; display: inline"><font color="white"> (проходит обучение)</font></div>';
            $eventsLink .= '<br>';
        }

        return $eventsLink;
    }

    public function beforeSave($insert)
    {
        $parts = ForeignEventParticipants::find()->all();
        $current = ForeignEventParticipants::find()->where(['id' => $this->id])->one();
        foreach ($parts as $part)
        {
            if (!($this->is_true == 1 && $current->is_true == 0))
                if ($part->firstname == $this->firstname && $part->secondname == $this->secondname && $part->patronymic == $this->patronymic && $part->birthdate !== $this->birthdate)
                {
                    $this->is_true = 0;
                    $part->is_true = 0;
                }

        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }
}
