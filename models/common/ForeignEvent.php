<?php

namespace app\models\common;

use app\models\components\FileWizard;
use Yii;

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
            [['company_id', 'event_way_id', 'event_level_id', 'min_participants_age', 'max_participants_age', 'business_trip', 'escort_id', 'order_participation_id', 'order_business_trip_id'], 'integer'],
            [['start_date', 'finish_date'], 'safe'],
            [['name', 'city', 'key_words', 'docs_achievement'], 'string', 'max' => 1000],
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
            'start_date' => 'Дата начала',
            'finish_date' => 'Дата окончания',
            'city' => 'Город',
            'event_way_id' => 'Формат проведения',
            'event_level_id' => 'Уровень',
            'min_participants_age' => 'Мин. возраст участников',
            'max_participants_age' => 'Макс. возраст участников',
            'business_trip' => 'Командировка',
            'escort_id' => 'Сопровождающий',
            'order_participation_id' => 'Приказ об участии',
            'order_business_trip_id' => 'Приказ о командировке',
            'key_words' => 'Ключевые слова',
            'docs_achievement' => 'Документы о достижениях',
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

    public function afterSave($insert, $changedAttributes)
    {
        $this->uploadTeacherParticipants();
        $this->uploadParticipantFiles();
        $this->uploadParticipantAchievement();
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
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
                $part->save();
            }
        }
    }
}
