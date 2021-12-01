<?php

namespace app\models\work;

use app\models\common\Company;
use app\models\common\DocumentOrder;
use app\models\common\EventLevel;
use app\models\common\EventWay;
use app\models\common\ForeignEvent;
use app\models\common\ParticipantAchievement;
use app\models\common\ParticipantFiles;
use app\models\common\People;
use app\models\common\TeacherParticipant;
use app\models\common\Team;
use app\models\components\FileWizard;
use Yii;
use yii\helpers\Html;


class ForeignEventWork extends ForeignEvent
{
    public $participants;
    public $achievement;
    public $team;

    public $docsAchievement;

    public function rules()
    {
        return [
            [['name', 'company_id', 'start_date', 'finish_date', 'event_way_id', 'event_level_id', 'min_participants_age', 'max_participants_age', 'business_trip', 'order_participation_id', 'key_words', 'docs_achievement'], 'required'],
            [['company_id', 'event_way_id', 'event_level_id', 'min_participants_age', 'max_participants_age', 'business_trip', 'escort_id', 'order_participation_id', 'order_business_trip_id', 'participantCount', 'copy'], 'integer'],
            [['start_date', 'finish_date'], 'safe'],
            [['name', 'city', 'key_words', 'docs_achievement', 'companyString', 'participants'], 'string', 'max' => 1000],
            [['docs_achievement'], 'file', 'extensions' => 'jpg, png, pdf, ppt, pptx, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxSize' => 26214400, 'maxFiles' => 10],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['event_way_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventWay::className(), 'targetAttribute' => ['event_way_id' => 'id']],
            [['event_level_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventLevel::className(), 'targetAttribute' => ['event_level_id' => 'id']],
            [['order_participation_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_participation_id' => 'id']],
            [['order_business_trip_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_business_trip_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Название',
            'company_id' => 'Организатор',
            'companyString' => 'Организатор',
            'start_date' => 'Дата начала',
            'finish_date' => 'Дата окончания',
            'city' => 'Город',
            'event_way_id' => 'Формат проведения',
            'eventWayString' => 'Формат проведения',
            'event_level_id' => 'Уровень',
            'eventLevelString' => 'Уровень',
            'ageRange' => 'Возраст участников',
            'min_participants_age' => 'Мин. возраст участников (лет)',
            'max_participants_age' => 'Макс. возраст участников (лет)',
            'business_trip' => 'Командировка',
            'businessTrip' => 'Командировка',
            'escort_id' => 'Сопровождающий',
            'order_participation_id' => 'Приказ об участии',
            'orderParticipationString' => 'Приказ об участии',
            'order_business_trip_id' => 'Приказ о командировке',
            'orderBusinessTripString' => 'Приказ о командировке',
            'key_words' => 'Ключевые слова',
            'docs_achievement' => 'Документы о достижениях',
            'participantsLink' => 'Участники',
            'achievementsLink' => 'Достижения',
            'docString' => 'Документ о достижениях',
            'docsAchievement' => 'Документ о достижениях',
            'teachers' => 'Педагоги',
            'winners' => 'Победители',
            'prizes' => 'Призеры',
            'businessTrips' => 'Командировка',
            'participantCount' => 'Кол-во участников',
            'participants' => 'Участники',
        ];
    }

    public function getEventWayString()
    {
        return $this->eventWay->name;
    }

    public function getEventLevelString()
    {
        return $this->eventLevel->name;
    }

    public function getAgeRange()
    {
        return $this->min_participants_age.' - '.$this->max_participants_age.' (лет)';
    }

    public function getBusinessTrip()
    {
        return $this->business_trip == 0 ? 'Нет' : 'Да';
    }

    /**
     * Gets query for [[EventLevel]].
     *
     * @return string
     */

    public function getColor($participant_id, $branch_id)
    {
        $groupsParticipant = TrainingGroupParticipantWork::find()->where(['participant_id' => $participant_id])->all();
        $groupSet = TrainingGroupWork::find();
        $now = date("Y-m-d");
        $flag = false;
        foreach ($groupsParticipant as $groupParticipant)
        {
            $group = $groupSet->where(['id' => $groupParticipant->branch_id])->one();
            if ($group->branch_id === $branch_id && date('Y-m-d', strtotime($group->finish_date . '+6 month')) >= $now)
            {
                $flag = true;
            }
        }
        if ($flag === false)
            return 'style = "background-color: #FCF8E3; margin: 0; whi"';
        else
            return 'style = "margin: 0;"';
    }

    public function getParticipantsLink()
    {
        $parts = TeacherParticipantWork::find()->where(['foreign_event_id' => $this->id])->all();
        $partsLink = '';
        $branchSet =  BranchWork::find();
        foreach ($parts as $partOne)
        {
            $partsLink .= '<p ' . $this->getColor($partOne->participant_id, $partOne->branch_id) . '>';
            $team = TeamWork::find()->where(['foreign_event_id' => $this->id])->andWhere(['participant_id' => $partOne->participant_id])->one();
            $partsLink = $partsLink.Html::a($partOne->participantWork->shortName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $partOne->participant_id])).' (педагог(-и): '.Html::a($partOne->teacherWork->shortName, \yii\helpers\Url::to(['people/view', 'id' => $partOne->teacher_id]));
            if ($partOne->teacher2_id !== null) $partsLink .= ' '.Html::a($partOne->teacher2Work->shortName, \yii\helpers\Url::to(['people/view', 'id' => $partOne->teacher2_id]));
            $branch = $branchSet->where(['id' => $partOne->branch_id])->one();
            $partsLink .= ', отдел для учета: ' . Html::a($branch->name, \yii\helpers\Url::to(['branch/view', 'id' => $branch->id]));
            $partsLink .= ')';
            if ($team !== null)
                $partsLink = $partsLink.' - Команда '.$team->name;
            $partsLink .= '</p>';

        }
        var_dump($partsLink);
        return $partsLink;
    }

    public function getAchievementsLink()
    {
        $parts = ParticipantAchievementWork::find()->where(['foreign_event_id' => $this->id])->orderBy(['winner' => SORT_DESC])->all();
        $partsLink = '';
        foreach ($parts as $partOne)
        {
            $value = $partOne->winner == 1 ? 'Победитель: ' : 'Призер: ';

            $partsLink = $partsLink. $value .Html::a($partOne->participantWork->shortName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $partOne->participant_id])).' &mdash; '.$partOne->achievment.'<br>';
        }
        return $partsLink;
    }

    public function getOrderParticipationString()
    {
        $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $this->order_participation_id])->one();
        return Html::a($order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
    }

    public function getOrderBusinessTripString()
    {
        $order = \app\models\work\DocumentOrderWork::find()->where(['id' => $this->order_business_trip_id])->one();
        return Html::a($order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
    }

    public function getDocString()
    {
        return Html::a($this->docs_achievement, \yii\helpers\Url::to(['foreign-event/get-file', 'fileName' => $this->docs_achievement, 'type' => 'achievements_files']));
    }


    public function getEscort()
    {
        return PeopleWork::find()->where(['id' => $this->escort_id])->one();
    }

    public function getParticipantCount()
    {
        return count(TeacherParticipant::find()->where(['foreign_event_id' => $this->id])->all());
    }

    public function getTeachers()
    {
        $teachers = TeacherParticipantWork::find()->select(['teacher_id'])->where(['foreign_event_id' => $this->id])->distinct()->all();
        $teacherList = '';
        foreach ($teachers as $teacherOne)
        {
            $teacherList = $teacherList.$teacherOne->teacherWork->shortName.'<br>';
        }
        return $teacherList;
    }

    public function getWinners()
    {
        $parts = ParticipantAchievementWork::find()->where(['foreign_event_id' => $this->id])->andWhere(['winner' => 1])->all();
        $partsList = '';
        foreach ($parts as $partOne)
        {
            $partsList = $partsList.$partOne->participantWork->shortName.'<br>';
        }
        return $partsList;
    }

    public function getPrizes()
    {
        $parts = ParticipantAchievementWork::find()->where(['foreign_event_id' => $this->id])->andWhere(['winner' => 0])->all();
        $partsList = '';
        foreach ($parts as $partOne)
        {
            $partsList = $partsList.$partOne->participantWork->shortName.'<br>';
        }
        return $partsList;
    }

    public function getBusinessTrips()
    {
        return $this->business_trip == 1 ? 'Да' : 'Нет';
    }

    public function uploadAchievementsFile()
    {
        $path = '@app/upload/files/foreign_event/achievements_files/';
        $date = $this->start_date;
        $new_date = '';
        for ($i = 0; $i < strlen($date); ++$i)
            if ($date[$i] != '-')
                $new_date = $new_date.$date[$i];
        $filename = '';
        $filename = 'Д.'.$new_date.'_'.$this->name;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^a-zA-Zа-яА-Я0-9._]{1}', '', $res);
        $res = FileWizard::CutFilename($res);
        $this->docs_achievement = $res . '.' . $this->docsAchievement->extension;
        $this->docsAchievement->saveAs( $path . $res . '.' . $this->docsAchievement->extension);
    }

    public function beforeSave($insert)
    {
        if ($this->business_trip == 0)
        {
            $this->order_business_trip_id = null;
            $this->escort_id = null;
        }
        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
        $this->uploadTeacherParticipants();
        $this->uploadParticipantFiles();
        $this->uploadParticipantAchievement();
        $this->uploadParticipantTeam();
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
    }

    private function uploadParticipantTeam()
    {
        if ($this->participants !== null)
        {
            foreach ($this->participants as $partOne)
            {
                if (strlen($partOne->team) > 0)
                {
                    $part = new Team();
                    $part->foreign_event_id = $this->id;
                    $part->participant_id = $partOne->fio;
                    $part->name = $partOne->team;
                    $part->save();
                }

            }
        }
    }

    private function uploadTeacherParticipants()
    {
        if ($this->participants !== null)
        {
            foreach ($this->participants as $participantOne)
            {
                $part = new TeacherParticipant();
                $part->foreign_event_id = $this->id;
                $part->participant_id = $participantOne->fio;
                $part->teacher_id = $participantOne->teacher;
                $part->teacher2_id = $participantOne->teacher2;
                $part->branch_id = $participantOne->branch;
                $part->focus = $participantOne->focus;
                $part->save();
            }
        }
    }

    private function uploadParticipantFiles()
    {
        if ($this->participants)
        {
            foreach ($this->participants as $participantOne)
            {
                $part = new ParticipantFiles();
                $part->foreign_event_id = $this->id;
                $part->participant_id = $participantOne->fio;
                $part->filename = $participantOne->fileString;
                $part->save();
            }
        }
    }

    private function uploadParticipantAchievement()
    {
        if ($this->achievement)
        {
            foreach ($this->achievement as $achievementOne)
            {
                $part = new ParticipantAchievement();
                $part->foreign_event_id = $this->id;
                $part->participant_id = $achievementOne->fio;
                $part->achievment = $achievementOne->achieve;
                $part->winner = $achievementOne->winner;
                $part->save();
            }
        }
    }

    public function beforeDelete()
    {
        $this->deleteParticipantAchievement();
        $this->deleteTeacherParticipant();
        $this->deleteParticipantFile();
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }

    private function deleteTeacherParticipant()
    {
        $tp = TeacherParticipant::find()->where(['foreign_event_id' => $this->id])->all();
        foreach ($tp as $tpOne) { $tpOne->delete(); }
    }

    private function deleteParticipantAchievement()
    {
        $pa = ParticipantAchievement::find()->where(['foreign_event_id' => $this->id])->all();
        foreach ($pa as $paOne) { $paOne->delete(); }
    }

    private function deleteParticipantFile()
    {
        $pf = ParticipantFiles::find()->where(['foreign_event_id' => $this->id])->all();
        foreach ($pf as $pfOne) { $pfOne->delete(); }
    }
}
