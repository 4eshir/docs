<?php

//--g
namespace app\models\components;

use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

use PhpOffice\PhpWord\Writer\PDF;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Html;

class WordWizard
{

    static public function Enrolment ($order_id)
    {
        ini_set('memory_limit', '512M');

        //$inputType = \PHPWord_IOFactory::identify(Yii::$app->basePath.'/templates/order_Enrolment.xlsx');
        //$reader = \PHPExcel_IOFactory::createReader($inputType);
        //$inputData = $reader->load(Yii::$app->basePath.'/templates/order_Enrolment.xlsx');

        /*$order = DocumentOrderWork::find()->where(['id' => $order_id])->one();
        $groups = OrderGroupWork::find()->where(['document_order_id' => $order->id])->all();
        $pastaAlDente = OrderGroupParticipantWork::find();
        $program = TrainingProgramWork::find();
        $teacher = TeacherGroupWork::find();
        $trG = TrainingGroupWork::find();
        $part = ForeignEventParticipantsWork::find();
        $gPart = TrainingGroupParticipantWork::find();
        $res = ResponsibleWork::find()->where(['document_order_id' => $order->id])->all();

        $c = 31;

        $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, 8, $order->order_date);
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(2, 8, $order->order_number . '/' . $order->order_copy_id . '/' .  $order->order_postfix);
        $text = '';
        foreach ($groups as $group)
        {
            $teacherTrG = $teacher->where(['training_group_id' => $group->training_group_id])->one();
            $text .= $teacherTrG->teacherWork->shortName . ', ';
        }
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, 15, '2. Назначить ' . $text . 'руководителем учебной группы, указанной в Приложении к настоящему приказу.');
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, 16, '3. ' . $text . 'обеспечить:');
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(2, 26, mb_substr($order->bring->firstname, 0, 1).'. '.mb_substr($order->bring->patronymic, 0, 1).'. '.$order->bring->secondname);
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(2, 27, mb_substr($order->executor->firstname, 0, 1).'. '.mb_substr($order->executor->patronymic, 0, 1).'. '.$order->executor->secondname);
        for ($i = 0; $i != count($res); $i++, $c++)
        {
            $fio = mb_substr($res[$i]->people->firstname, 0, 1) .'. '. mb_substr($res[$i]->people->patronymic, 0, 1) .'. '. $res[$i]->people->secondname;
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, $c, '«____» ________ 20__ г.');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(2, $c, $fio);
        }
        $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, 77, 'от ' . $order->order_date . ' № ' . $order->order_number . '/' . $order->order_copy_id . '/' .  $order->order_postfix);
        $c = 80;

        foreach ($groups as $group)
        {
            $trGroup = $trG->where(['id' => $group->training_group_id])->one();
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, $c, 'Учебная группа: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $trGroup->number);
            $c++;
            $teacherTrG = $teacher->where(['training_group_id' => $group->training_group_id])->one();
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Руководитель: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $teacherTrG->teacherWork->shortName);
            $c++;
            $programTrG = $program->where(['id' => $trGroup->training_program_id])->one();
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Дополнительная общеразвивающая программа: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $programTrG->name);
            $c++;
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Направленность: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $programTrG->stringFocus);
            $c++;
            $out = '';
            if ($programTrG->allow_remote == 0) $out = 'Только очная форма';
            if ($programTrG->allow_remote == 1) $out = 'Очная форма, с применением дистанционных технологий';
            if ($programTrG->allow_remote == 2) $out = 'Только дистанционная форма';
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Форма обучения: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $out);
            $c++;
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Срок освоения: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'c ' . $trGroup->start_date . ' до ' . $trGroup->finish_date);
            $c++;
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Дата зачисления: ');
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $order->order_date);
            $c++;
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, 'Обучающиеся: ');
            $pasta = $pastaAlDente->where(['order_group_id' => $group->id])->all();
            foreach ($pasta as $macaroni)
            {
                $groupParticipant = $gPart->where(['id' => $macaroni->group_participant_id])->one();
                $participant = $part->where(['id' => $groupParticipant->participant_id])->one();
                $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, $c, $participant->getFullName());
                $c++;
            }
            $c = $c + 2;
        }*/

        //header("Pragma: public");
        //header("Expires: 0");
        //header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        //header("Content-Type: application/force-download");
        //header("Content-Type: application/octet-stream");
        //header("Content-Type: application/download");;
        //header("Content-Disposition: attachment;filename=order_Enrolment.xls");
        //header("Content-Transfer-Encoding: binary ");

        $reader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
        $inputData = $reader->load(Yii::$app->basePath.'\templates\enrolment3.docx');
        //$inputData->setDefaultFontName('Times New Roman');
        //$inputData->setDefaultFontSize(14);
        $text = "Добавление тестового текста";

        $section = $inputData->addSection();
        $section->addText($text);

        /*$rendererName = \PhpOffice\PhpWord\Settings::PDF_RENDERER_TCPDF;
        $rendererLibraryPath = realpath(Yii::$app->basePath . '/../vendor/tecnick.com/tcpdf');
        \PhpOffice\PhpWord\Settings::setPdfRenderer($rendererName, $rendererLibraryPath);
        $writer = new PDF($phpWord);*/

        //\PhpOffice\PhpWord\Settings::setPdfRendererPath('tcpdf_min');
        //\PhpOffice\PhpWord\Settings::setPdfRendererName('TCPDF');


        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="first.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        //header('Content-Type: application/pdf');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');


        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($inputData, 'Word2007');
        //$writer = \PhpOffice\PhpWord\IOFactory::createWriter($inputData, 'PDF');
        //$writer->save("test.pdf");
        $writer->save("php://output");
        exit;
    }

    static private function Deduction ($order_id) {

    }
}