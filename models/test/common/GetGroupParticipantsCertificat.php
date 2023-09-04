<?php

namespace app\models\test\common;

use Yii;

/**
 * This is the model class for table "get_group_participants_certificat".
 *
 * @property int $id
 * @property int|null $certificat_number
 * @property int|null $training_group_participant_id
 *
 * @property GetGroupParticipantsTrainingGroupParticipant $trainingGroupParticipant
 */
class GetGroupParticipantsCertificat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'get_group_participants_certificat';
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
            [['certificat_number', 'training_group_participant_id'], 'integer'],
            [['training_group_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => GetGroupParticipantsTrainingGroupParticipant::className(), 'targetAttribute' => ['training_group_participant_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'certificat_number' => 'Certificat Number',
            'training_group_participant_id' => 'Training Group Participant ID',
        ];
    }

    /**
     * Gets query for [[TrainingGroupParticipant]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupParticipant()
    {
        return $this->hasOne(GetGroupParticipantsTrainingGroupParticipant::className(), ['id' => 'training_group_participant_id']);
    }
}
