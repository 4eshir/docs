<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_group_participants_training_group_participant".
 *
 * @property int $id
 * @property int|null $participant_id
 * @property int|null $training_group_id
 *
 * @property GetGroupParticipantsForeignEventParticipant $participant
 * @property GetGroupParticipantsTrainingGroup $trainingGroup
 */
class GetGroupParticipantsTrainingGroupParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_group_participants_training_group_participant';
    }

    /**
     * @return \yii\db\Connection the database connection used by this AR class.
     */
    public static function getDb()
    {
        return Yii::$app->get('db_report_test');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['participant_id', 'training_group_id'], 'integer'],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => GetGroupParticipantsForeignEventParticipant::className(), 'targetAttribute' => ['participant_id' => 'id']],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => GetGroupParticipantsTrainingGroup::className(), 'targetAttribute' => ['training_group_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'participant_id' => 'Participant ID',
            'training_group_id' => 'Training Group ID',
        ];
    }

    /**
     * Gets query for [[Participant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipant()
    {
        return $this->hasOne(GetGroupParticipantsForeignEventParticipant::className(), ['id' => 'participant_id']);
    }

    /**
     * Gets query for [[TrainingGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroup()
    {
        return $this->hasOne(GetGroupParticipantsTrainingGroup::className(), ['id' => 'training_group_id']);
    }
}
