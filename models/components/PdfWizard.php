<?php

namespace app\models\components;

use app\models\work\CertificatWork;
use app\models\work\TrainingGroupParticipantWork;
use Yii;
use kartik\mpdf\Pdf;

class PdfWizard
{

    static public function actionMpdfBlog($id) {
        /*$this->layout = 'pdf';
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');

        $model = $this->findModel($id);

        //$model = $this->findModel();
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
            'content' => $this->render('viewpdf', ['model'=>$model]),
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.img-circle {border-radius: 50%;}',
            'options' => [
                'title' => $model->title,
                'subject' => 'PDF'
            ],
            'methods' => [
                'SetHeader' => ['<img src="/images/inspire2_logo_20.png" class="img-circle"> Школа брейк данса INSPIRE||inspire2.ru'],
                'SetFooter' => ['|{PAGENO}|'],
            ]
        ]);
        return $pdf->render();*/
        $content = '<br><p><strong style="color:#ffa500;">Что нового узнает ребенок?</strong></p>
					<p>Летом в наших детских технопарках бурлят детские смены по робототехнике, физике, 3D-графике и созданию игр, программированию, беспилотным летательным аппаратам, химии, нанотехнологиям, биологии, разработке виртуальной реальности, электронике, судомоделированию, общетехническому моделированию и конструированию, шитью для начинающих, декоративно-прикладному творчеству.</p>
					
					<p><br><strong style="color:#0088bf;">Зачем детям Технолето?</strong></p>
					<p>Чтобы незабываемо, интересно и с пользой провести летние каникулы, окунуться в атмосферу творчества, открыть неожиданные перспективы, развить таланты и найти новых реальных друзей. А еще Технолето - это уникальная возможность для ребят попробовать себя в роди исследователей, разработчиков и изобретателей и поработать на оборудовании, которого нет в обычных школах.</p>

					<p><br><strong style="color:#E4282B;">А когда начинаются и оканчиваются смены Технолета?</strong></p>
					<p>Первая смена: 6 июня – 17 июня;<br>Вторая смена: 20 июня – 1 июля;<br>Третья смена: 4 июля – 15 июля;<br>Четвертая смена: 18 июля – 29 июля.</p>';
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $pdf = new Pdf([
            //'mode' => Pdf::MODE_CORE, // leaner size using standard fonts
            //'destination' => Pdf::DEST_BROWSER,
            'content' => $content,
            'options' => [
                // any mpdf options you wish to set
            ],
            'methods' => [
                'SetTitle' => 'Privacy Policy - Krajee.com',
                'SetSubject' => 'Generating PDF files via yii2-mpdf extension has never been easy',
                'SetHeader' => ['Krajee Privacy Policy||Generated On: ' . date("r")],
                'SetFooter' => ['|Page {PAGENO}|'],
                'SetAuthor' => 'Kartik Visweswaran',
                'SetCreator' => 'Kartik Visweswaran',
                'SetKeywords' => 'Krajee, Yii2, Export, PDF, MPDF, Output, Privacy, Policy, yii2-mpdf',
            ]
        ]);
        return $pdf->render();
    }

    static public function DownloadCertificat ($certificat_id)
    {
        $certificat = CertificatWork::find()->where(['id' => $certificat_id])->one();
        $part = TrainingGroupParticipantWork::find()->where(['id' => $certificat->training_group_participant_id])->one();

        $content = '<body style="
                                 background: url('. Yii::$app->basePath . '/upload/files/certificat_templates/' . $certificat->certificatTemplate->path .') no-repeat ;
                                 background-size: cover;">
            <div>
             <p style="font-size: 19px;">Министерство образования и науки Астраханской области
                <br>государственное автономное образовательное учреждение Астраханской области 
                <br>дополнительного образования "Региональный школьный технопарк"
                <br>отдел "Кванториум" ГАОУ АО ДО "РШТ"
             </p>
             <p style="font-size: 19px; color: gray">24 мая 2022 года</p>
             <p style="font-size: 60px; color: #427fa2; font-style: italic;">СЕРТИФИКАТ</p>
             <p style="font-size: 16px; font-style: italic;">удостоверяет, что</p>
             <p style="font-size: 37px; text-decoration: none; color: black;">'.$part->participantWork->fullName.'</p>
             <p style="font-size: 19px; text-align: justify;">успешно прошел обучение по дополнительной общеразвивающей программе
                <br>"Основы конструирования и пилотирования мультикоптеров", выполнил 
                <br>научно-технический проект "Разработка прототипа квадрокоптера с 
                <br>эхолотом для изучения рельефа дна водоемов" и выступил на научной 
                <br>конференции "SсhoolTech Conference".
             </p>
             <p>Рег. номер '.$certificat->certificat_number.'</p>
             <img src="'.Yii::$app->basePath . '/upload/files/certificat_templates/' .'pict.png">
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
        $mpdf->Output('Сертификат №'. $certificat->certificat_number . '_'. $part->participantWork->fullName .'.pdf', 'D'); // call the mpdf api output as needed
        exit;
    }
}