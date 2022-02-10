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
            'teachers' => 'Педагоги',
            'winners' => 'Победители',
            'prizes' => 'Призеры',
            'businessTrips' => 'Командировка',
            'participantCount' => 'Кол-во участников',
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


    /**
     * Gets query for [[EventLevel]].
     *
     * @return string
     */

}
