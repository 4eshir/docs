<?php

namespace app\models\work;

use app\models\common\Certificat;
use app\models\work\CertificatTemplatesWork;
use app\models\work\TrainingGroupParticipantWork;

use Yii;

/**
 * This is the model class for table "certificat".
 *
 * @property int $id
 * @property int $certificat_number
 * @property int $certificat_template_id
 * @property int $training_group_participant_id
 *
 * @property CertificatTemplates $certificatTemplate
 * @property TrainingGroupParticipant $trainingGroupParticipant
 */
class CertificatWork extends Certificat
{
    public $group_id;
    public $participant_id;
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
            [['certificat_number', 'certificat_template_id', 'training_group_participant_id', 'group_id'], 'integer'],
            ['participant_id', 'safe'],
            [['certificat_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => CertificatTemplatesWork::className(), 'targetAttribute' => ['certificat_template_id' => 'id']],
            [['training_group_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupParticipantWork::className(), 'targetAttribute' => ['training_group_participant_id' => 'id']],
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

    public function mass_save()
    {
        $allCert = CertificatWork::find()->orderBy(['certificat_number' => SORT_DESC])->all();
        $startNumber = $allCert[0]->certificat_number + 1;
        $tc = 0;
        if ($this->participant_id != null)
        {
            for ($i = 0; $i < count($this->participant_id); $i++)
            {
                if ($this->participant_id[$i] != 0)
                {
                    $cert = new CertificatWork();
                    $cert->certificat_number = $startNumber + $tc;
                    $cert->certificat_template_id = $this->certificat_template_id;
                    $cert->training_group_participant_id = $this->participant_id[$i];
                    $cert->save();
                    $tc++;
                }
                
            }
        }
        
    }
}
