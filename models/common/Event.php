<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "event".
 *
 * @property int $id
 * @property string $name
 * @property string|null $old_name
 * @property string $start_date
 * @property string $finish_date
 * @property int $event_type_id
 * @property int $event_form_id
 * @property int $format
 * @property string $address
 * @property int $event_level_id
 * @property int $participants_count
 * @property int $is_federal
 * @property int $responsible_id
 * @property int|null $responsible2_id
 * @property string $key_words
 * @property string $comment
 * @property int|null $order_id
 * @property int|null $regulation_id
 * @property int $contains_education
 * @property string|null $protocol
 * @property string|null $photos
 * @property string|null $reporting_doc
 * @property string|null $other_files
 * @property int|null $event_way_id
 * @property int|null $creator_id
 * @property int|null $last_edit_id
 * @property int|null $participation_scope_id
 *
 * @property EventForm $eventForm
 * @property ParticipationScope $participationScope
 * @property EventLevel $eventLevel
 * @property EventType $eventType
 * @property People $responsible
 * @property People $responsible2
 * @property DocumentOrder $order
 * @property Regulation $regulation
 * @property EventWay $eventWay
 * @property User $creator
 * @property User $lastEdit
 * @property EventBranch[] $eventBranches
 * @property EventErrors[] $eventErrors
 * @property EventParticipants[] $eventParticipants
 * @property EventTrainingGroup[] $eventTrainingGroups
 * @property EventsLink[] $eventsLinks
 * @property TemporaryJournal[] $temporaryJournals
 */
class Event extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'start_date', 'finish_date', 'event_type_id', 'event_form_id', 'address', 'event_level_id', 'participants_count', 'is_federal', 'responsible_id', 'key_words', 'comment', 'contains_education'], 'required'],
            [['start_date', 'finish_date'], 'safe'],
            [['event_type_id', 'event_form_id', 'format', 'event_level_id', 'participants_count', 'is_federal', 'responsible_id', 'responsible2_id', 'order_id', 'regulation_id', 'contains_education', 'event_way_id', 'creator_id', 'last_edit_id', 'participation_scope_id'], 'integer'],
            [['name', 'old_name', 'address', 'key_words', 'comment', 'protocol', 'photos', 'reporting_doc', 'other_files'], 'string', 'max' => 1000],
            [['event_form_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventForm::className(), 'targetAttribute' => ['event_form_id' => 'id']],
            [['participation_scope_id'], 'exist', 'skipOnError' => true, 'targetClass' => ParticipationScope::className(), 'targetAttribute' => ['participation_scope_id' => 'id']],
            [['event_level_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventLevel::className(), 'targetAttribute' => ['event_level_id' => 'id']],
            [['event_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventType::className(), 'targetAttribute' => ['event_type_id' => 'id']],
            [['responsible_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['responsible_id' => 'id']],
            [['responsible2_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['responsible2_id' => 'id']],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => DocumentOrder::className(), 'targetAttribute' => ['order_id' => 'id']],
            [['regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regulation::className(), 'targetAttribute' => ['regulation_id' => 'id']],
            [['event_way_id'], 'exist', 'skipOnError' => true, 'targetClass' => EventWay::className(), 'targetAttribute' => ['event_way_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['last_edit_id' => 'id']],
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
            'old_name' => 'Old Name',
            'start_date' => 'Start Date',
            'finish_date' => 'Finish Date',
            'event_type_id' => 'Event Type ID',
            'event_form_id' => 'Event Form ID',
            'format' => 'Format',
            'address' => 'Address',
            'event_level_id' => 'Event Level ID',
            'participants_count' => 'Participants Count',
            'is_federal' => 'Is Federal',
            'responsible_id' => 'Responsible ID',
            'responsible2_id' => 'Responsible 2 ID',
            'key_words' => 'Key Words',
            'comment' => 'Comment',
            'order_id' => 'Order ID',
            'regulation_id' => 'Regulation ID',
            'contains_education' => 'Contains Education',
            'protocol' => 'Protocol',
            'photos' => 'Photos',
            'reporting_doc' => 'Reporting Doc',
            'other_files' => 'Other Files',
            'event_way_id' => 'Event Way ID',
            'creator_id' => 'Creator ID',
            'participation_scope_id' => 'Participation Scope ID',
        ];
    }

    /**
     * Gets query for [[EventForm]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventForm()
    {
        return $this->hasOne(EventForm::className(), ['id' => 'event_form_id']);
    }

    /**
     * Gets query for [[ParticipationScope]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipationScope()
    {
        return $this->hasOne(ParticipationScope::className(), ['id' => 'participation_scope_id']);
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
     * Gets query for [[EventType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventType()
    {
        return $this->hasOne(EventType::className(), ['id' => 'event_type_id']);
    }

    /**
     * Gets query for [[Responsible]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsible()
    {
        return $this->hasOne(People::className(), ['id' => 'responsible_id']);
    }

    /**
     * Gets query for [[Responsible2]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsible2()
    {
        return $this->hasOne(People::className(), ['id' => 'responsible2_id']);
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(DocumentOrder::className(), ['id' => 'order_id']);
    }

    /**
     * Gets query for [[Regulation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegulation()
    {
        return $this->hasOne(Regulation::className(), ['id' => 'regulation_id']);
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
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[EventBranches]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventBranches()
    {
        return $this->hasMany(EventBranch::className(), ['event_id' => 'id']);
    }

    /**
     * Gets query for [[EventErrors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventErrors()
    {
        return $this->hasMany(EventErrors::className(), ['event_id' => 'id']);
    }

    /**
     * Gets query for [[EventParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventParticipants()
    {
        return $this->hasMany(EventParticipants::className(), ['event_id' => 'id']);
    }

    /**
     * Gets query for [[EventTrainingGroups]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventTrainingGroups()
    {
        return $this->hasMany(EventTrainingGroup::className(), ['event_id' => 'id']);
    }

    /**
     * Gets query for [[EventsLinks]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventsLinks()
    {
        return $this->hasMany(EventsLink::className(), ['event_id' => 'id']);
    }

    /**
     * Gets query for [[TemporaryJournals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemporaryJournals()
    {
        return $this->hasMany(TemporaryJournal::className(), ['event_id' => 'id']);
    }
}
