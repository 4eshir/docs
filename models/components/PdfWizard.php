<?php

namespace app\models\components;

use app\models\work\CertificatWork;
use app\models\work\TrainingGroupParticipantWork;
use Yii;
use kartik\mpdf\Pdf;
use yii\helpers\FileHelper;

class PdfWizard
{
    static public function rus2translit($string) {
        $converter = array(
            'а' => 'a',   'б' => 'b',   'в' => 'v',
            'г' => 'g',   'д' => 'd',   'е' => 'e',
            'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
            'и' => 'i',   'й' => 'j',   'к' => 'k',
            'л' => 'l',   'м' => 'm',   'н' => 'n',
            'о' => 'o',   'п' => 'p',   'р' => 'r',
            'с' => 's',   'т' => 't',   'у' => 'u',
            'ф' => 'f',   'х' => 'h',   'ц' => 'c',
            'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
            'ь' => '\'',  'ы' => 'y',   'ъ' => '\'',
            'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

            'А' => 'A',   'Б' => 'B',   'В' => 'V',
            'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
            'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
            'И' => 'I',   'Й' => 'J',   'К' => 'K',
            'Л' => 'L',   'М' => 'M',   'Н' => 'N',
            'О' => 'O',   'П' => 'P',   'Р' => 'R',
            'С' => 'S',   'Т' => 'T',   'У' => 'U',
            'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
            'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
            'Ь' => '\'',  'Ы' => 'Y',   'Ъ' => '\'',
            'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
        );
        return strtr($string, $converter);
    }

    static public function DownloadCertificat ($certificat_id, $destination, $path = null)
    {
        $certificat = CertificatWork::find()->where(['certificat_id' => $certificat_id])->one();
        $part = TrainingGroupParticipantWork::find()->where(['id' => $certificat->training_group_participant_id])->one();
        if ($part->participantWork->sex == "Женский")
        {
            $genderVerbs = ['прошла', 'выполнила', 'выступила'];
        }
        else
            $genderVerbs = ['прошел', 'выполнил', 'выступил'];


        $date = $part->trainingGroupWork->protection_date;
        $certificatText = '';
        if ($part->trainingGroupWork->trainingProgram->certificatType->id == 1)
            $certificatText = ', ' . $genderVerbs[1] . ' '.mb_strtolower($part->groupProjectThemes->projectType->name).' проект "'
                            . $part->groupProjectThemes->projectTheme->name . '" и ' . $genderVerbs[2] . ' на научной конференции "SсhoolTech Conference".';
        if ($part->trainingGroupWork->trainingProgram->certificatType->id == 2)
            $certificatText = ', ' . $genderVerbs[1] . ' итоговую контрольную работу с оценкой '
                            . $part->points .' из 100 баллов.';

        $trainedText = 'успешно '. $genderVerbs[0] . ' обучение по дополнительной общеразвивающей программе 
                            "'.$part->trainingGroupWork->programNameNoLink.'" в объеме '.$part->trainingGroupWork->trainingProgram->capacity .' ак. ч.'. $certificatText;
        $size = 19;

        if (strlen($trainedText) >= 650)
        {
            $size = 17;
            if (strlen($trainedText) >= 920)
            {
                $size = 15;
                if (strlen($trainedText) >= 1070)
                    $size = 13;
            }
        }

var_dump(Yii::$app->basePath . '/upload/files/certificat_templates/' . $certificat->certificatTemplate->path);
var_dump($certificat->id);
        $content = '<body style="
                                 background: url('. Yii::$app->basePath . '/upload/files/certificat_templates/' . $certificat->certificatTemplate->path . ') no-repeat ;
                                 background-size: 10%;">
            <div>
            <table>
                <tr>
                    <td style="width: 780px; height: 130px; font-size: 19px; vertical-align: top;">
                        Министерство образования и науки Астраханской области<br>
                        государственное автономное образовательное учреждение Астраханской области<br>
                        дополнительного образования "Региональный школьный технопарк"<br>
                        отдел "'. $part->trainingGroupWork->pureBranch .'" ГАОУ АО ДО "РШТ"<br>
                    </td>
                </tr>
                <tr>
                    <td style="width: 700px; font-size: 19px; color: #626262;">
                        '. date("d", strtotime($date)) . ' '
                        . WordWizard::Month(date("m", strtotime($date))) . ' '
                        . date("Y", strtotime($date)) . ' года
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 56px; height: 110px; vertical-align: bottom; color: #427fa2;">
                        СЕРТИФИКАТ
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 15px; font-style: italic; height: 50px; vertical-align: bottom;">
                        удостоверяет, что
                    </td>
                </tr>
                <tr>
                    <td style="font-size: 28px; text-decoration: none; color: black; font-weight: bold;">
                        '. $part->participantWork->fullName .'
                    </td>
                </tr>
                <tr>
                    <td style="line-height: 3ex; font-size: '.$size.'px; text-align: justify; text-justify: inter-word; height: 160px; vertical-align: bottom;">
                            '. $trainedText .'
                    </td>
                </tr>
                </table><table>
                <tr>
                    <td style="width: 850px; font-size: 20px; vertical-align: bottom">
                        Рег. номер '.$certificat->certificatLongNumber.'
                    </td>
                    <td style="width: 180px; font-size: 18px; vertical-align: bottom">
                        В.В. Войков <br>
                        директор <br>
                        ГАОУ АО ДО "РШТ" <br>
                        г. Астрахань - ' . date("Y", strtotime($date)) . '
                    </td>
                    <td style="">
                       <img width="282" height="202" src="'.Yii::$app->basePath . '/templates/' .'seal.png">
                    </td>
                </tr>
            </table>
            </div>
            </body>';

        $pdf = $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
            'destination' => Pdf::DEST_BROWSER,
            'options' => [
                // any mpdf options you wish to set
            ],
            'orientation' => Pdf::ORIENT_LANDSCAPE,
            'methods' => [
                'SetTitle' => 'Privacy Policy - Krajee.com',
                'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetFooter' => ['|Page {PAGENO}|'],
                'SetAuthor' => 'ЦСХД (с) РШТ',
                'SetCreator' => 'ЦСХД (с) РШТ',
                'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);

        $mpdf = $pdf->api; // fetches mpdf api
        $mpdf->WriteHtml($content); // call mpdf write html

        if ($destination == 'download')
        {
            $mpdf->Output('Сертификат №'. $certificat->certificatLongNumber . ' '. $part->participantWork->fullName .'.pdf', 'D'); // call the mpdf api output as needed
            exit;
        }
        else {
            $certificatName = 'Certificat #'. $certificat->certificatLongNumber . ' '. PdfWizard::rus2translit($part->participantWork->fullName);
            if ($path == null)
                $mpdf->Output(Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'/'. $certificatName . '.pdf', 'F'); // call the mpdf api output as needed
            else
                $mpdf->Output($path . $certificatName . '.pdf', 'F');
            //$mpdf->Output(Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'/Certificat '. $certificat->certificatLongNumber . '.pdf', \Mpdf\Output\Destination::FILE);
            return $certificatName;
        }
    }

}