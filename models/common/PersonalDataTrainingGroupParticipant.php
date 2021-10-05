<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "personal_data_training_group_participant".
 *
 * @property int $id
 * @property int $training_group_participant_id
 * @property int $personal_data_id
 * @property int $status
 *
 * @property PersonalData $personalData
 * @property TrainingGroupParticipant $trainingGroupParticipant
 */
class PersonalDataTrainingGroupParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'personal_data_training_group_participant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_group_participant_id', 'personal_data_id', 'status'], 'required'],
            [['training_group_participant_id', 'personal_data_id', 'status'], 'integer'],
            [['personal_data_id'], 'exist', 'skipOnError' => true, 'targetClass' => PersonalData::className(), 'targetAttribute' => ['personal_data_id' => 'id']],
            [['training_group_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupParticipant::className(), 'targetAttribute' => ['training_group_participant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'training_group_participant_id' => 'Training Group Participant ID',
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
     * Gets query for [[TrainingGroupParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupParticipant()
    {
        return $this->hasOne(TrainingGroupParticipant::className(), ['id' => 'training_group_participant_id']);
    }
}
