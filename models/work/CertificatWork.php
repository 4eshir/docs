<?php

namespace app\models\work;

use app\models\common\Certificat;
use app\models\common\ForeignEventParticipants;
use app\models\components\PdfWizard;
use app\models\work\CertificatTemplatesWork;
use app\models\work\TrainingGroupParticipantWork;

use Yii;
use yii\helpers\FileHelper;
use yii\helpers\Html;
use app\models\components\createDirZip;


class CertificatWork extends Certificat
{
    public $group_id;
    public $participant_id;

    public function rules()
    {
        return [
            [['certificat_number', 'certificat_template_id', 'training_group_participant_id'], 'required'],
            [['certificat_number', 'certificat_template_id', 'training_group_participant_id', 'group_id'], 'integer'],
            [['participant_id', 'boobs'], 'safe'],
            [['certificat_template_id'], 'exist', 'skipOnError' => true, 'targetClass' => CertificatTemplatesWork::className(), 'targetAttribute' => ['certificat_template_id' => 'id']],
            [['training_group_participant_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroupParticipantWork::className(), 'targetAttribute' => ['training_group_participant_id' => 'id']],
        ];
    }


    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'certificat_number' => 'Номер сертификата',
            'certificatLongNumber' => 'Номер сертификата',
            'certificatView' => 'Номер сертификата',
            'certificat_template_id' => 'Шаблон сертификата',
            'certificatTemplateName' => 'Шаблон сертификата',
            'training_group_participant_id' => 'Учащийся',
            'participantName' => 'Учащийся',
            'participantGroup' => 'Учебная группа учащегося',
            'pdfFile' => 'Сертификат',
        ];
    }

    public function getCertificatLongNumber()
    {
        $result = sprintf('%06d', $this->certificat_number);
        return $result;
    }

    public function getCertificatView()
    {
        return Html::a($this->getCertificatLongNumber(), \yii\helpers\Url::to(['certificat/view', 'id' => $this->id]));
    }

    public function getPdfFile()
    {
        $result = Html::a("Скачать pdf-файл", \yii\helpers\Url::to(['certificat/generation-pdf', 'certificat_id' => $this->id]), ['class'=>'btn btn-success']);
        return $result;
    }

    public function  getParticipantGroup()
    {
        $part = TrainingGroupParticipantWork::find()->where(['id' => $this->training_group_participant_id])->one();
        $result = Html::a($part->trainingGroupWork->number, \yii\helpers\Url::to(['training-group/view', 'id' => $part->training_group_id]));
        return $result;
    }

    public function getParticipantName()
    {
        $part = TrainingGroupParticipantWork::find()->where(['id' => $this->training_group_participant_id])->one();
        $result = Html::a($part->participantWork->fullName, \yii\helpers\Url::to(['foreign-event-participants/view', 'id' => $part->participant_id]));
        return $result;
    }

    public function getCertificatTemplateName()
    {
        $templates = CertificatTemplatesWork::find()->where(['id' => $this->certificat_template_id])->one();
        $result = Html::a($templates->name, \yii\helpers\Url::to(['certificat-templates/view', 'id' => $templates->id]));
        return $result;
    }

    public function mass_save()
    {
        FileHelper::createDirectory(Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'/');
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
                    PdfWizard::DownloadCertificat($cert->certificat_number, 'server');
                    $tc++;
                }
            }
        }
        $this->archiveDownload();
    }


    public function archiveDownload()
    {
        $path = Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'/';
        $createZip = new createDirZip();
        $createZip->get_files_from_folder($path, '');
        $fileName = 'archive_certificats.zip';

        $fd = fopen ($fileName, 'wb');  //wb
        $out = fwrite ($fd, $createZip->getZippedfile());
        fclose ($fd);
        $createZip->forceDownload($fileName);
        FileHelper::removeDirectory(Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'/');
    }
}
