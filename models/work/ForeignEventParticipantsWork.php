<?php

namespace app\models\work;

use app\models\common\ForeignEventParticipants;
use app\models\common\ParticipantAchievement;
use app\models\common\ParticipantFiles;
use app\models\common\TeacherParticipant;
use app\models\common\TrainingGroupParticipant;
use Yii;
use yii\helpers\Html;


class ForeignEventParticipantsWork extends ForeignEventParticipants
{
    public $similar = [];
    public $pd = [];



    public function rules()
    {
        return [
            [['firstname', 'secondname', 'sex'], 'required'],
            [['is_true', 'guaranted_true'], 'integer'],
            [['pd'], 'safe'],
            [['firstname', 'secondname', 'patronymic', 'birthdate', 'sex'], 'string'],
        ];
    }

    public function getPersonalData()
    {
        $result = "<table class='table table-bordered' style='width: 600px'>";
        $pds = PersonalDataForeignEventParticipantWork::find()->where(['foreign_event_participant_id' => $this->id])->orderBy(['id' => SORT_ASC])->all();
        foreach ($pds as $pd)
        {
            $result .= '<tr><td style="width: 350px">';
            if ($pd->status == 0) $result .= $pd->personalData->name.'</td><td style="width: 250px"><span class="badge badge-success">Разрешено</span></td>';
            else $result .= $pd->personalData->name.'</td><td style="width: 250px"><span class="badge badge-error">Запрещено</span></td>';
            $result .= '</td></tr>';
        }
        $result .= "</table>";
        return $result;
    }

    public function getFullName()
    {
        return $this->secondname.' '.$this->firstname.' '.$this->patronymic;
    }

    public function getFullNameAndBirthday()
    {
        return $this->secondname.' '.$this->firstname.' '.$this->patronymic . ' (Дата рождения: ' . $this->birthdate . ')';
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
                $eventsLink .= ' (группа завершила обучение)';
            else
                $eventsLink .= ' <div style="background-color: green; display: inline"><font color="white"> (проходит обучение)</font></div>';

            if ($event->status === 2)
                $eventsLink .= ' | Переведен';

            $eventsLink .= '<br>';
        }

        return $eventsLink;
    }

    public function beforeSave($insert)
    {
        $parts = ForeignEventParticipants::find()->all();
        $current = ForeignEventParticipants::find()->where(['id' => $this->id])->one();

        $newDate = new \DateTime('-3 year');
        if ($this->birthdate < '1996-01-01' || $this->birthdate > $newDate->format('Y-m-d'))
            $this->is_true = 0;
        else
            $this->is_true = 1;

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
            $newDate = new \DateTime('-3 year');
            if (($parts[$i]->birthdate < '1996-01-01' || $parts[$i]->birthdate > $newDate->format('Y-m-d')) & $parts[$i]->guaranted_true !== 1)
            {
                $parts[$i]->is_true = 0;
                $parts[$i]->guaranted_true = 0;
                $parts[$i]->save();
            }

            if ($parts[$i]->birthdate < '1930-01-01' || $parts[$i]->birthdate > $newDate->format('Y-m-d'))
            {
                $parts[$i]->is_true = 0;
                $parts[$i]->guaranted_true = 0;
                $parts[$i]->save();
            }


            if ($parts[$i]->secondname == $parts[$i + 1]->secondname && $parts[$i]->firstname == $parts[$i + 1]->firstname && $parts[$i]->patronymic == $parts[$i + 1]->patronymic)
            {
                $parts[$i]->guaranted_true = 0;
                $parts[$i + 1]->guaranted_true = 0;
                $parts[$i]->is_true = 0;
                $parts[$i + 1]->is_true = 0;
                $parts[$i]->save();
                $parts[$i + 1]->save();
            }
        }

        $newDate = new \DateTime('-3 year');
        if ($parts[count($parts) - 1]->birthdate < '1996-01-01' || $parts[count($parts) - 1]->birthdate > $newDate->format('Y-m-d'))
        {
            $parts[count($parts) - 1]->is_true = 0;
            $parts[count($parts) - 1]->guaranted_true = 0;
            $parts[count($parts) - 1]->save();
        }
    }

    public function beforeDelete()
    {
        $partsData = PersonalDataForeignEventParticipantWork::find()->where(['foreign_event_participant_id' => $this->id])->all();
        foreach ($partsData as $pd)
        {
            $pd->delete();
        }

        $teams = TeamWork::find()->where(['participant_id' => $this->id])->all();
        foreach ($teams as $team) {
            $team->delete();
        }
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {

        $data = PersonalDataWork::find()->all();
        foreach ($data as $one)
        {
            $partData = PersonalDataForeignEventParticipantWork::find()->where(['foreign_event_participant_id' => $this->id])->andWhere(['personal_data_id' => $one->id])->one();
            if ($partData === null)
            {
                $partData = new PersonalDataForeignEventParticipantWork();
                $partData->foreign_event_participant_id = $this->id;
                $partData->personal_data_id = $one->id;
            }
            if ($this->pd !== "" && array_search($one->id, $this->pd) !== false)
                $partData->status = 1;
            else
                $partData->status = 0;
            $partData->save();
        }
    }
}
