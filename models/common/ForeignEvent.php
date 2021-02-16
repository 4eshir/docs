<?php

namespace app\models\common;

use app\models\components\FileWizard;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "foreign_event".
 *
 * @property int $id
 * @property string $name
 * @property int $company_id
 * @property string $start_date
 * @property string $finish_date
 * @property string|null $city
 * @property int $event_way_id
 * @property int $event_level_id
 * @property int $min_participants_age
 * @property int $max_participants_age
 * @property int $business_trip
 * @property int|null $escort_id
 * @property int $order_participation_id
 * @property int|null $order_business_trip_id
 * @property string $key_words
 * @property string $docs_achievement
 * @property int $copy
 *
 * @property Company $company
 * @property EventWay $eventWay
 * @property EventLevel $eventLevel
 * @property DocumentOrder $orderParticipation
 * @property DocumentOrder $orderBusinessTrip
 * @property ParticipantAchievement[] $participantAchievements
 * @property ParticipantFiles[] $participantFiles
 * @property ParticipantForeignEvent[] $participantForeignEvents
 * @property TeacherParticipant[] $teacherParticipants
 */
class ForeignEvent extends \yii\db\ActiveRecord
{
    public $participants;
    public $achievement;
    public $team;

    public $docsAchievement;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'foreign_event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'company_id', 'start_date', 'finish_date', 'event_way_id', 'event_level_id', 'min_participants_age', 'max_participants_age', 'business_trip', 'order_participation_id', 'key_words', 'docs_achievement'], 'required'],
            [['company_id', 'event_way_id', 'event_level_id', 'min_participants_age', 'max_participants_age', 'business_trip', 'escort_id', 'order_participation_id', 'order_business_trip_id', 'participantCount', 'copy'], 'integer'],
            [['start_date', 'finish_date'], 'safe'],
            [['name', 'city', 'key_words', 'docs_achievement', 'companyString', 'participants'], 'string', 'max' => 1000],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['event_way_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventWay::className(), 'targetAttribute' => ['event_way_id' => 'id']],
            [['event_level_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventLevel::className(), 'targetAttribute' => ['event_level_id' => 'id']],
            [['order_participation_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_participation_id' => 'id']],
            [['order_business_trip_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_business_trip_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * Gets query for [[EventWay]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventWay()
    {
        return $this->hasOne(EventWay::className(), ['id' => 'event_way_id']);
    }

    /**
     * Gets query for [[EventLevel]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventLevel()
    {
        return $this->hasOne(EventLevel::className(), ['id' => 'event_level_id']);
    }

    /**
     * Gets query for [[OrderParticipation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderParticipation()
    {
        return $this->hasOne(DocumentOrder::className(), ['id' => 'order_participation_id']);
    }

    /**
     * Gets query for [[OrderBusinessTrip]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderBusinessTrip()
    {
        return $this->hasOne(DocumentOrder::className(), ['id' => 'order_business_trip_id']);
    }

    /**
     * Gets query for [[ParticipantAchievements]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipantAchievements()
    {
        return $this->hasMany(ParticipantAchievement::className(), ['foreign_event_id' => 'id']);
    }

    /**
     * Gets query for [[ParticipantFiles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipantFiles()
    {
        return $this->hasMany(ParticipantFiles::className(), ['foreign_event_id' => 'id']);
    }

    /**
     * Gets query for [[ParticipantForeignEvents]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipantForeignEvents()
    {
        return $this->hasMany(ParticipantForeignEvent::className(), ['foreign_event_id' => 'id']);
    }

    /**
     * Gets query for [[TeacherParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeacherParticipants()
    {
        return $this->hasMany(TeacherParticipant::className(), ['foreign_event_id' => 'id']);
    }

    /**
     * Gets query for [[EventLevel]].
     *
     * @return string
     */

    public function getCompanyString()
    {
        return $this->company->name;
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

    public function getParticipantsLink()
    {
        $parts = TeacherParticipant::find()->where(['foreign_event_id' => $this->id])->all();
        $partsLink = '';
        foreach ($parts as $partOne)
        {
            $team = Team::find()->where(['foreign_event_id' => $this->id])->andWhere(['participant_id' => $partOne->participant_id])->one();
            $partsLink = $partsLink.Html::a($partOne->participant->shortName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $partOne->participant_id])).' (педагог: '.Html::a($partOne->teacher->shortName, \yii\helpers\Url::to(['people/view', 'id' => $partOne->teacher_id])).')';
            if ($team !== null)
                $partsLink = $partsLink.' - Команда '.$team->name;
            $partsLink .= '<br>';

        }
        return $partsLink;
    }

    public function getAchievementsLink()
    {
        $parts = ParticipantAchievement::find()->where(['foreign_event_id' => $this->id])->orderBy(['winner' => SORT_DESC])->all();
        $partsLink = '';
        foreach ($parts as $partOne)
        {
            $value = $partOne->winner == 1 ? 'Победитель: ' : 'Призер: ';
            $partsLink = $partsLink. $value .Html::a($partOne->participant->shortName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $partOne->participant_id])).' &mdash; '.$partOne->achievment.'<br>';
        }
        return $partsLink;
    }

    public function getOrderParticipationString()
    {
        $order = \app\models\common\DocumentOrder::find()->where(['id' => $this->order_participation_id])->one();
        return Html::a($order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
    }

    public function getOrderBusinessTripString()
    {
        $order = \app\models\common\DocumentOrder::find()->where(['id' => $this->order_business_trip_id])->one();
        return Html::a($order->fullName, \yii\helpers\Url::to(['document-order/view', 'id' => $order->id]));
    }

    public function getDocString()
    {
        return Html::a($this->docs_achievement, \yii\helpers\Url::to(['foreign-event/get-file', 'fileName' => $this->docs_achievement, 'type' => 'achievements_files']));
    }


    public function getEscort()
    {
        return People::find()->where(['id' => $this->escort_id])->one();
    }

    public function getParticipantCount()
    {
        return count(TeacherParticipant::find()->where(['foreign_event_id' => $this->id])->all());
    }

    public function getTeachers()
    {
        $teachers = TeacherParticipant::find()->select(['teacher_id'])->where(['foreign_event_id' => $this->id])->distinct()->all();
        $teacherList = '';
        foreach ($teachers as $teacherOne)
        {
            $teacherList = $teacherList.$teacherOne->teacher->shortName.'<br>';
        }
        return $teacherList;
    }

    public function getWinners()
    {
        $parts = ParticipantAchievement::find()->where(['foreign_event_id' => $this->id])->andWhere(['winner' => 1])->all();
        $partsList = '';
        foreach ($parts as $partOne)
        {
            $partsList = $partsList.$partOne->participant->shortName.'<br>';
        }
        return $partsList;
    }

    public function getPrizes()
    {
        $parts = ParticipantAchievement::find()->where(['foreign_event_id' => $this->id])->andWhere(['winner' => 0])->all();
        $partsList = '';
        foreach ($parts as $partOne)
        {
            $partsList = $partsList.$partOne->participant->shortName.'<br>';
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
