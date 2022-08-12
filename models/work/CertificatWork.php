<?php

namespace app\models\work;

use app\models\common\Certificat;
use app\models\common\ForeignEventParticipants;
use app\models\work\CertificatTemplatesWork;
use app\models\work\TrainingGroupParticipantWork;

use Yii;


class CertificatWork extends Certificat
{
    public $group_id;
    public $participant_id;

    public function rules()
    {
        return [
            [['certificat_number', 'certificat_template_id', 'training_group_participant_id'], 'required'],
            [['certificat_number', 'certificat_template_id', 'training_group_participant_id', 'group_id'], 'integer'],
            [['participant_id'], 'safe'],
            [['certificat_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => CertificatTemplatesWork::className(), 'targetAttribute' => ['certificat_template_id' => 'id']],
            [['training_group_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupParticipantWork::className(), 'targetAttribute' => ['training_group_participant_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'certificat_number' => 'Номер сертификата',
            'certificat_template_id' => 'Шаблон сертификата',
            'training_group_participant_id' => 'Учащийся',
            'participantName' => 'Учащийся',
        ];
    }

    public function getParticipantName()
    {
        //$part = TrainingGroupParticipantWork::find()->where(['id' => $this->training_group_participant_id])->one();
        //$result = Html::a($part->participantWork->fullName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $part->participant_id]));
        //var_dump('booobs');
        return 'booobs';
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
