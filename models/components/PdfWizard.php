<?php

namespace app\models\components;

use app\models\extended\AccessTrainingGroup;
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
        $certificat = CertificatWork::find()->where(['id' => $certificat_id])->one();
        if (strripos($certificat->certificatTemplate->name, 'лето'))
        {
            if (strripos($certificat->certificatTemplate->name, 'Интенсив'))
                return PdfWizard::CertificatIntensives($certificat_id, $destination, $path);
            else
                return PdfWizard::CertificatTechnoSummer($certificat_id, $destination, $path);
        }
        else if (strripos($certificat->certificatTemplate->name, 'школа'))
            return PdfWizard::CertificatSchool($certificat_id, $destination, $path);
        else
            return PdfWizard::CertificatStandard($certificat_id, $destination, $path);
    }

    static private function CertificatStandard ($certificat_id, $destination, $path = null)
    {
        $certificat = CertificatWork::find()->where(['id' => $certificat_id])->one();
        $part = TrainingGroupParticipantWork::find()->where(['id' => $certificat->training_group_participant_id])->one();
        if ($part->participantWork->sex == "Женский")
        {
            $genderVerbs = ['прошла', 'выполнила', 'выступила', 'представила'];
        }
        else
            $genderVerbs = ['прошел', 'выполнил', 'выступил', 'представил'];


        $date = $part->trainingGroupWork->protection_date;
        $certificatText = '';
        if ($part->trainingGroupWork->trainingProgram->certificatType->id == 1)
            $certificatText = ', ' . $genderVerbs[1] . ' '.mb_strtolower($part->groupProjectThemes->projectType->name).' проект "'
                . $part->groupProjectThemes->projectTheme->name . '" и ' . $genderVerbs[2] . ' на научной конференции "SсhoolTech Conference".';
        if ($part->trainingGroupWork->trainingProgram->certificatType->id == 2)
            $certificatText = ', ' . $genderVerbs[1] . ' итоговую контрольную работу с оценкой '
                . $part->points .' из 100 баллов.';
        if ($part->trainingGroupWork->trainingProgram->certificatType->id == 4)
            $certificatText = ', ' . $genderVerbs[1] . ' '.mb_strtolower($part->groupProjectThemes->projectType->name).' проект "'
                . $part->groupProjectThemes->projectTheme->name . '" и ' . $genderVerbs[3] . ' его в публичном выступлении на открытом уроке.';

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

        $content = '<body style="
                                 background: url('. Yii::$app->basePath . '/upload/files/certificat-templates/' . $certificat->certificatTemplate->path . ') no-repeat ;
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
                    <td style="width: 180px; font-size: 18px; vertical-align: bottom">';
        if ($date >= "2022-12-07" && $date <= "2022-12-23" && $date != "2022-12-12")
            $content .= '
                        Е.В. Киселев <br>
                        и.о. директора <br>
                        ГАОУ АО ДО "РШТ" <br>
                        г. Астрахань - ' . date("Y", strtotime($date)) . '
                    </td>
                    <td style="">
                       <img width="332" height="202" src="'.Yii::$app->basePath . '/templates/' .'seal2.png">';
        else
            $content .= '
                        В.В. Войков <br>
                        директор <br>
                        ГАОУ АО ДО "РШТ" <br>
                        г. Астрахань - ' . date("Y", strtotime($date)) . '
                    </td>
                    <td style="">
                       <img width="282" height="202" src="'.Yii::$app->basePath . '/templates/' .'seal.png">';
        $content .= '
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
        $mpdf->setProtection(array('print', 'print-highres'));

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

    static private function CertificatTechnoSummer ($certificat_id, $destination, $path = null)
    {
        $certificat = CertificatWork::find()->where(['id' => $certificat_id])->one();
        $part = TrainingGroupParticipantWork::find()->where(['id' => $certificat->training_group_participant_id])->one();

        $content = '<body style="font-family: sans-serif; 
                                 background: url('. Yii::$app->basePath . '/upload/files/certificat-templates/' . $certificat->certificatTemplate->path . ') no-repeat ;">
            <div>
                <p style="height: 160px;"></p>
                <p style="font-size: 28px; text-decoration: none; color: #164192; font-weight: bold; padding-left: -5px;">'. $part->participantWork->fullName .'</p>
            </div>
            <div>
                <p style="height: 293px;"></p>
                <p style="padding-left: 120px; font-size: 20px; vertical-align: bottom; color: #164192; ">'.$certificat->certificatLongNumber.'</p>
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
        $mpdf->setProtection(array('print', 'print-highres'));

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

    static private function CertificatIntensives ($certificat_id, $destination, $path = null)
    {
        $certificat = CertificatWork::find()->where(['id' => $certificat_id])->one();
        $part = TrainingGroupParticipantWork::find()->where(['id' => $certificat->training_group_participant_id])->one();

        if ($part->participantWork->sex == "Женский")
            $genderVerbs = ['прошла', 'выполнила', 'выступила'];
        else
            $genderVerbs = ['прошел', 'выполнил', 'выступил'];

        $date = $part->trainingGroupWork->protection_date;
        $type = strripos($certificat->certificatTemplate->name, 'Плюс') ? 'ИНТЕНСИВ+' : 'ИНТЕНСИВ';

        $style = 'padding-left: -15px; margin: 10px;';
        $styleDistance = 'height: 1px; margin: 10px;';


        $content = '<body style="font-family: sans-serif; background: url('. Yii::$app->basePath . '/upload/files/certificat-templates/' . $certificat->certificatTemplate->path . ') no-repeat ;">
            <div>';
        if ($date >= "2023-07-21")
            $content .= '<p style="'.$styleDistance.'"></p>
                         <p style="font-size: 16px;'.$style.' padding-top: -20px;">
                            Министерство образования и науки Астраханской области<br>
                            государственное автономное образовательное учреждение Астраханской области<br>
                            дополнительного образования "Региональный школьный технопарк"<br>
                            отдел "'. $part->trainingGroupWork->pureBranch .'" ГАОУ АО ДО "РШТ"<br></p>';
        else
            $content .= '<p style="'.$style.' padding-top: -10px;"><img width="535" height="110" src="'.Yii::$app->basePath . '/upload/files/certificat-templates/' .'seal.png"></p>';//<p style="height: 100px"></p>
        $content .= '
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 18px; '.$style.'">'. date("d", strtotime($date)) . ' '
                    . WordWizard::Month(date("m", strtotime($date))) . ' '
                    . date("Y", strtotime($date)) . ' года
            </p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 24px; font-weight: bold;'.$style.'">'. $part->participantWork->fullName .'</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 16px;'.$style.'">'.$genderVerbs[0].' очное обучение по программе мероприятия</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 24px;'.$style.'">'.$type.'</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 16px;'.$style.'">в объеме '.$part->trainingGroupWork->trainingProgram->capacity .' академических часов, '.$genderVerbs[1].' проект</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 20px; width: 800px;'.$style.'">"'.$part->groupProjectThemes->projectTheme->name.'"</p>
            <p style="font-size: 16px;'.$style.'">и '.$genderVerbs[2].' с итоговой презентацией на научной конференции<br><span style="font-weight: bold;">Schooltech Conference.</span></p>
            <p style="height: 70px;"></p>
            <p style="width: 600px; border-bottom: 1px solid black; margin: 0; padding-left: -40px; font-size: 2px;"></p>
            <p style="font-size: 14px; '.$style.'">В.В. Войков <br>
                        Директор <br>
                        ГАОУ АО ДО "РШТ"</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 14px; color: #585858;'.$style.'">Рег. номер '.$certificat->certificatLongNumber.'</p>
            </div>
            </body>';

        $pdf = new Pdf([
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
        $mpdf->setProtection(array('print', 'print-highres'));

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
            return $certificatName;
        }
    }

    static private function CertificatSchool ($certificat_id, $destination, $path = null)
    {
        $certificat = CertificatWork::find()->where(['id' => $certificat_id])->one();
        $part = TrainingGroupParticipantWork::find()->where(['id' => $certificat->training_group_participant_id])->one();

        if ($part->participantWork->sex == "Женский")
            $genderVerbs = ['прошла', 'приняла'];
        else
            $genderVerbs = ['прошел', 'принял'];

        $date = $part->trainingGroupWork->protection_date;

        $style = 'padding-left: -15px; margin: 10px;';
        $styleDistance = 'height: 1px; margin: 10px;';

        $content = '<body style="font-family: sans-serif; background: url('. Yii::$app->basePath . '/upload/files/certificat-templates/' . $certificat->certificatTemplate->path . ') no-repeat ;">
            <div>';
        if ($date >= "2023-07-21")
            $content .= '<p style="'.$styleDistance.'"></p>
                         <p style="font-size: 16px;'.$style.' padding-top: -20px;">
                            Министерство образования и науки Астраханской области<br>
                            государственное автономное образовательное учреждение Астраханской области<br>
                            дополнительного образования "Региональный школьный технопарк"<br>
                            отдел "'. $part->trainingGroupWork->pureBranch .'" ГАОУ АО ДО "РШТ"<br></p>';
        else
            $content .= '<p style="'.$style.' padding-top: -10px;"><img width="535" height="110" src="'.Yii::$app->basePath . '/upload/files/certificat-templates/' .'seal.png"></p>';//<p style="height: 100px"></p>
        $content .= '
            <p style="'.$styleDistance.'"></p><p style="height: 20px;"></p>
            <p style="font-size: 18px; '.$style.'">'. date("d", strtotime($date)) . ' '
            . WordWizard::Month(date("m", strtotime($date))) . ' '
            . date("Y", strtotime($date)) . ' года
            </p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 24px; font-weight: bold;'.$style.'">'. $part->participantWork->fullName .'</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 16px;'.$style.'">'.$genderVerbs[0].' очное обучение по программе мероприятия</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 24px;'.$style.'">ЛЕТНЯЯ ШКОЛА</p>
            <p style="font-size: 20px;'.$style.'">"'.$part->trainingGroupWork->programNameNoLink.'"</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 16px;'.$style.'">в объеме '.$part->trainingGroupWork->trainingProgram->capacity .' академических часов</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 16px;'.$style.'">и '.$genderVerbs[1].' участие в итоговом конкурсе по решению криптографических задач.</span></p>
            <p style="height: 70px;"></p>
            <p style="width: 600px; border-bottom: 1px solid black; margin: 0; padding-left: -40px; font-size: 2px;"></p>
            <p style="font-size: 14px; '.$style.'">В.В. Войков <br>
                        Директор <br>
                        ГАОУ АО ДО "РШТ"</p>
            <p style="'.$styleDistance.'"></p>
            <p style="font-size: 14px; color: #585858;'.$style.'">Рег. номер '.$certificat->certificatLongNumber.'</p>
            </div>
            </body>';

        $pdf = new Pdf([
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
        $mpdf->setProtection(array('print', 'print-highres'));

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
            return $certificatName;
        }
    }
}