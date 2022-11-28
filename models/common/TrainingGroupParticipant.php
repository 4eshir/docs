<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "training_group_participant".
 *
 * @property int $id
 * @property int $participant_id
 * @property int|null $certificat_number
 * @property int|null $send_method_id
 * @property int $training_group_id
 * @property int $status
 * @property int|null $success
 * @property int|null $points
 * @property int|null $group_project_themes_id
 *
 * @property OrderGroupParticipant[] $orderGroupParticipants
 * @property ForeignEventParticipants $participant
 * @property SendMethod $sendMethod
 * @property TrainingGroup $trainingGroup
 * @property GroupProjectThemes $groupProjectThemes
 * @property Certificat $certificat
 */
class TrainingGroupParticipant extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'training_group_participant';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['participant_id', 'training_group_id'], 'required'],
            [['participant_id', 'certificat_number', 'send_method_id', 'training_group_id', 'status', 'success', 'points', 'group_project_themes_id'], 'integer'],
            [['participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => ForeignEventParticipants::className(), 'targetAttribute' => ['participant_id' => 'id']],
            [['send_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => SendMethod::className(), 'targetAttribute' => ['send_method_id' => 'id']],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroup::className(), 'targetAttribute' => ['training_group_id' => 'id']],
            [['group_project_themes_id'], 'exist', 'skipOnError' => true, 'targetClass' => GroupProjectThemes::className(), 'targetAttribute' => ['group_project_themes_id' => 'id']],
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
            'certificat_number' => 'Certificat Number',
            'send_method_id' => 'Send Method ID',
            'training_group_id' => 'Training Group ID',
            'status' => 'Status',
            'success' => 'Success',
            'points' => 'Points',
            'group_project_themes_id' => 'Group Project Themes ID',
        ];
    }

    /**
     * Gets query for [[OrderGroupParticipants]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrderGroupParticipants()
    {
        return $this->hasMany(OrderGroupParticipant::className(), ['group_participant_id' => 'id']);
    }

    /**
     * Gets query for [[Participant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getParticipant()
    {
        return $this->hasOne(ForeignEventParticipants::className(), ['id' => 'participant_id']);
    }

    /**
     * Gets query for [[SendMethod]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSendMethod()
    {
        return $this->hasOne(SendMethod::className(), ['id' => 'send_method_id']);
    }

    /**
     * Gets query for [[TrainingGroup]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroup()
    {
        return $this->hasOne(TrainingGroup::className(), ['id' => 'training_group_id']);
    }

    /**
     * Gets query for [[GroupProjectThemes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroupProjectThemes()
    {
        return $this->hasOne(GroupProjectThemes::className(), ['id' => 'group_project_themes_id']);
    }
}
