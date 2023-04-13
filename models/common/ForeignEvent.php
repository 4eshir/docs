<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "foreign_event".
 *
 * @property int $id
 * @property string $name
 * @property int|null $company_id
 * @property string $start_date
 * @property string $finish_date
 * @property string|null $city
 * @property int|null $event_way_id
 * @property int $event_level_id
 * @property int|null $min_participants_age
 * @property int|null $max_participants_age
 * @property int|null $business_trip
 * @property int|null $escort_id
 * @property int|null $order_participation_id
 * @property int|null $add_order_participation_id
 * @property int|null $order_business_trip_id
 * @property string|null $key_words
 * @property string|null $docs_achievement
 * @property int $copy
 * @property int|null $creator_id
 * @property int $is_minpros
 *
 * @property Company $company
 * @property EventWay $eventWay
 * @property EventLevel $eventLevel
 * @property DocumentOrder $orderParticipation
 * @property DocumentOrder $orderBusinessTrip
 * @property User $creator
 * @property DocumentOrder $addOrderParticipation
 * @property ForeignEventErrors[] $foreignEventErrors
 * @property ParticipantAchievement[] $participantAchievements
 * @property ParticipantFiles[] $participantFiles
 * @property ParticipantForeignEvent[] $participantForeignEvents
 * @property TeacherParticipant[] $teacherParticipants
 * @property Team[] $teams
 * @property TemporaryJournal[] $temporaryJournals
 */
class ForeignEvent extends \yii\db\ActiveRecord
{
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
            [['name', 'start_date', 'finish_date', 'event_level_id'], 'required'],
            [['company_id', 'event_way_id', 'event_level_id', 'min_participants_age', 'max_participants_age', 'business_trip', 'escort_id', 'order_participation_id', 'add_order_participation_id', 'order_business_trip_id', 'copy', 'creator_id', 'is_minpros'], 'integer'],
            [['start_date', 'finish_date'], 'safe'],
            [['name', 'city', 'key_words', 'docs_achievement'], 'string', 'max' => 1000],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['event_way_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventWay::className(), 'targetAttribute' => ['event_way_id' => 'id']],
            [['event_level_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventLevel::className(), 'targetAttribute' => ['event_level_id' => 'id']],
            [['order_participation_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_participation_id' => 'id']],
            [['order_business_trip_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_business_trip_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['creator_id' => 'id']],
            [['add_order_participation_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['add_order_participation_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
            'company_id' => 'Company ID',
            'start_date' => 'Start Date',
            'finish_date' => 'Finish Date',
            'city' => 'City',
            'event_way_id' => 'Event Way ID',
            'event_level_id' => 'Event Level ID',
            'min_participants_age' => 'Min Participants Age',
            'max_participants_age' => 'Max Participants Age',
            'business_trip' => 'Business Trip',
            'escort_id' => 'Escort ID',
            'order_participation_id' => 'Order Participation ID',
            'add_order_participation_id' => 'Add Order Participation ID',
            'order_business_trip_id' => 'Order Business Trip ID',
            'key_words' => 'Key Words',
            'docs_achievement' => 'Docs Achievement',
            'copy' => 'Copy',
            'creator_id' => 'Creator ID',
            'is_minpros' => 'Is Minpros',
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
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[AddOrderParticipation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAddOrderParticipation()
    {
        return $this->hasOne(DocumentOrder::className(), ['id' => 'add_order_participation_id']);
    }

    /**
     * Gets query for [[ForeignEventErrors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEventErrors()
    {
        return $this->hasMany(ForeignEventErrors::className(), ['foreign_event_id' => 'id']);
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
     * Gets query for [[Teams]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTeams()
    {
        return $this->hasMany(Team::className(), ['foreign_event_id' => 'id']);
    }

    /**
     * Gets query for [[TemporaryJournals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemporaryJournals()
    {
        return $this->hasMany(TemporaryJournal::className(), ['foreign_event_id' => 'id']);
    }
}
