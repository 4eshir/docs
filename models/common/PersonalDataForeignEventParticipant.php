<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "personal_data_foreign_event_participant".
 *
 * @property int $id
 * @property int $foreign_event_participant_id
 * @property int $personal_data_id
 * @property int $status
 *
 * @property PersonalData $personalData
 * @property ForeignEventParticipants $foreignEventParticipant
 */
class PersonalDataForeignEventParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'personal_data_foreign_event_participant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['foreign_event_participant_id', 'personal_data_id', 'status'], 'required'],
            [['foreign_event_participant_id', 'personal_data_id', 'status'], 'integer'],
            [['personal_data_id'], 'exist', 'skipOnError' => true, 'targetClass' => PersonalData::className(), 'targetAttribute' => ['personal_data_id' => 'id']],
            [['foreign_event_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::className(), 'targetAttribute' => ['foreign_event_participant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'foreign_event_participant_id' => 'Foreign Event Participant ID',
            'personal_data_id' => 'Personal Data ID',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[PersonalData]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPersonalData()
    {
        return $this->hasOne(PersonalData::className(), ['id' => 'personal_data_id']);
    }

    /**
     * Gets query for [[ForeignEventParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getForeignEventParticipant()
    {
        return $this->hasOne(ForeignEventParticipants::className(), ['id' => 'foreign_event_participant_id']);
    }
}
