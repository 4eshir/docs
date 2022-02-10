<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "temporary_journal".
 *
 * @property int $id
 * @property int $material_object_id
 * @property int $give_people_id
 * @property int $gain_people_id
 * @property string $date_issue
 * @property int $approximate_time
 * @property string|null $date_delivery
 * @property int $branch_id
 * @property int|null $auditorium_id
 * @property int $event_id
 * @property int $foreign_event_id
 * @property int $signed_give
 * @property int $signed_gain
 * @property string|null $comment
 * @property string|null $files
 *
 * @property Auditorium $auditorium
 * @property Branch $branch
 * @property ForeignEvent $foreignEvent
 * @property Event $event
 * @property People $gainPeople
 * @property People $givePeople
 * @property MaterialObject $materialObject
 */
class TemporaryJournal extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'temporary_journal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['material_object_id', 'give_people_id', 'gain_people_id', 'date_issue', 'approximate_time', 'branch_id', 'event_id', 'foreign_event_id'], 'required'],
            [['material_object_id', 'give_people_id', 'gain_people_id', 'approximate_time', 'branch_id', 'auditorium_id', 'event_id', 'foreign_event_id', 'signed_give', 'signed_gain'], 'integer'],
            [['date_issue', 'date_delivery'], 'safe'],
            [['comment', 'files'], 'string', 'max' => 1000],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::className(), 'targetAttribute' => ['auditorium_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['foreign_event_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEvent::className(), 'targetAttribute' => ['foreign_event_id' => 'id']],
            [['event_id'], 'exist', 'skipOnError' => true, 'targetClass' => Event::className(), 'targetAttribute' => ['event_id' => 'id']],
            [['gain_people_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['gain_people_id' => 'id']],
            [['give_people_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['give_people_id' => 'id']],
            [['material_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObject::className(), 'targetAttribute' => ['material_object_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'material_object_id' => 'Material Object ID',
            'give_people_id' => 'Give People ID',
            'gain_people_id' => 'Gain People ID',
            'date_issue' => 'Date Issue',
            'approximate_time' => 'Approximate Time',
            'date_delivery' => 'Date Delivery',
            'branch_id' => 'Branch ID',
            'auditorium_id' => 'Auditorium ID',
            'event_id' => 'Event ID',
            'foreign_event_id' => 'Foreign Event ID',
            'signed_give' => 'Signed Give',
            'signed_gain' => 'Signed Gain',
            'comment' => 'Comment',
            'files' => 'Files',
        ];
    }

    /**
     * Gets query for [[Auditorium]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditorium()
    {
        return $this->hasOne(Auditorium::className(), ['id' => 'auditorium_id']);
    }

    /**
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    /**
     * Gets query for [[ForeignEvent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEvent()
    {
        return $this->hasOne(ForeignEvent::className(), ['id' => 'foreign_event_id']);
    }

    /**
     * Gets query for [[Event]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEvent()
    {
        return $this->hasOne(Event::className(), ['id' => 'event_id']);
    }

    /**
     * Gets query for [[GainPeople]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGainPeople()
    {
        return $this->hasOne(People::className(), ['id' => 'gain_people_id']);
    }

    /**
     * Gets query for [[GivePeople]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGivePeople()
    {
        return $this->hasOne(People::className(), ['id' => 'give_people_id']);
    }

    /**
     * Gets query for [[MaterialObject]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getMaterialObject()
    {
        return $this->hasOne(MaterialObject::className(), ['id' => 'material_object_id']);
    }
}
