<?php

namespace app\models\common;

use Yii;
use yii\helpers\Html;


class ForeignEventParticipantsWork extends ForeignEventParticipants
{
    public $similar = [];

    public function rules()
    {
        return [
            [['firstname', 'secondname', 'sex'], 'required'],
            [['is_true', 'guaranted_true'], 'integer'],
            [['firstname', 'secondname', 'patronymic', 'birthdate', 'sex'], 'string'],
        ];
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
            if (/*!($this->is_true == 1 && $current->is_true == 0) && $this->is_true !== 2*/$this->guaranted_true !== 1)
                if ($part->firstname == $this->firstname && $part->secondname == $this->secondname && $part->patronymic == $this->patronymic && $part->birthdate !== $this->birthdate)
                {
                    $this->is_true = 0;
                    $this->similar[] = $part;
                }

        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function checkOther()
    {
        foreach ($this->similar as $sim) {
            $sim->is_true = 0;
            $sim->save();
        }
    }

    public function checkCorrect()
    {
        $parts = ForeignEventParticipants::find()->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC, 'patronymic' => SORT_ASC])->all();
        for ($i = 0; $i !== count($parts) - 1; $i++)
        {
            if ($parts[$i]->secondname == $parts[$i + 1]->secondname && $parts[$i]->firstname == $parts[$i + 1]->firstname && $parts[$i]->patronymic == $parts[$i + 1]->patronymic)
            {
                $parts[$i]->is_true = 0;
                $parts[$i + 1]->is_true = 0;
                $parts[$i]->save();
                $parts[$i + 1]->save();
            }
        }
    }
}