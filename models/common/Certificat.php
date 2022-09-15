<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "certificat".
 *
 * @property int $id
 * @property int $certificat_number
 * @property int $certificat_template_id
 * @property int $training_group_participant_id
 * @property int $status 0 - не отправлен, 1 - отправлен, 2 - ошибка отправки
 *
 * @property CertificatTemplates $certificatTemplate
 * @property TrainingGroupParticipant $trainingGroupParticipant
 */
class Certificat extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'certificat';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['certificat_number', 'certificat_template_id', 'training_group_participant_id'], 'required'],
            [['certificat_number', 'certificat_template_id', 'training_group_participant_id', 'status'], 'integer'],
            [['certificat_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => CertificatTemplates::className(), 'targetAttribute' => ['certificat_template_id' => 'id']],
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
            'certificat_number' => 'Certificat Number',
            'certificat_template_id' => 'Certificat Template ID',
            'training_group_participant_id' => 'Training Group Participant ID',
            'status' => 'Status',
        ];
    }

    /**
     * Gets query for [[CertificatTemplate]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCertificatTemplate()
    {
        return $this->hasOne(CertificatTemplates::className(), ['id' => 'certificat_template_id']);
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
