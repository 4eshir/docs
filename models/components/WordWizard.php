<?php

//--g
namespace app\models\components;

use app\models\work\DocumentOrderWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\OrderGroupParticipantWork;
use app\models\work\OrderGroupWork;
use app\models\components\petrovich\Petrovich;
use app\models\work\ResponsibleWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use app\models\work\TrainingProgramWork;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\PhpWord;

use PhpOffice\PhpWord\Writer\PDF;
use Yii;
use yii\db\ActiveQuery;
use yii\db\Query;
use yii\helpers\Html;

class WordWizard
{

    static public function Month($month)
    {
        if ($month === '01')
            return 'января';
        if ($month === '02')
            return 'февраля';
        if ($month === '03')
            return 'марта';
        if ($month === '04')
            return 'апреля';
        if ($month === '05')
            return 'мая';
        if ($month === '06')
            return 'июня';
        if ($month === '07')
            return 'июля';
        if ($month === '08')
            return 'августа';
        if ($month === '09')
            return 'сентября';
        if ($month === '10')
            return 'октября';
        if ($month === '11')
            return 'ноября';
        if ($month === '12')
            return 'декабря';
        else return '______';
    }

    static public function convertMillimetersToTwips($millimeters)
    {
        return floor($millimeters * 56.7);
    }

    static public function Enrolment ($order_id)
    {
        ini_set('memory_limit', '512M');

        $inputData = new PhpWord();
        //$reader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
        //$inputData = $reader->load(Yii::$app->basePath.'\templates\order_study.docx');
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
                                                'marginLeft' => WordWizard::convertMillimetersToTwips(30),
                                                'marginBottom' => WordWizard::convertMillimetersToTwips(20),
                                                'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addText('Региональный', array('name' => 'Calibri', 'size' => '16'));
        $cell = $table->addCell(2000, array('borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText(' школьный', array('name' => 'Calibri', 'size' => '16'));
        $cell = $table->addCell(22000, array('valign' => 'bottom', 'borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText('  414000, г. Астрахань, ул. Адмиралтейская, д. 21, помещение № 66', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addImage(Yii::$app->basePath.'/templates/logo.png', array('width'=>100, 'height'=>40, 'align'=>'left'));
        $cell = $table->addCell(2000, array('valign' => 'top'));
        $cell->addText('технопарк', array('name' => 'Calibri', 'size' => '16'), array('align' => 'center'));
        $cell = $table->addCell(22000);
        $cell->addText(' +7 8512 442428 • info@schooltech.ru• www.школьныйтехнопарк.рф', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right'));
        //----------
        $section->addTextBreak(2);
        $section->addText('ПРИКАЗ', array('bold' => true), array('align' => 'center'));
        $section->addTextBreak(1);

        /*----------------*/
        $order = DocumentOrderWork::find()->where(['id' => $order_id])->one();
        $groups = OrderGroupWork::find()->where(['document_order_id' => $order->id])->all();
        $pastaAlDente = OrderGroupParticipantWork::find();
        $program = TrainingProgramWork::find();
        $teacher = TeacherGroupWork::find();
        $trG = TrainingGroupWork::find();
        $part = ForeignEventParticipantsWork::find();
        $gPart = TrainingGroupParticipantWork::find();
        $res = ResponsibleWork::find()->where(['document_order_id' => $order->id])->all();

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('«' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г.');
        $cell = $table->addCell(12000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText($text, null, array('align' => 'right'));
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText($order->order_name, null, array('align' => 'left'));
        $cell = $table->addCell(6000);
        $cell->addTextBreak(1);

        $section->addTextBreak(1);
        $text = '          В соответствии с ч. 1, ч. 2 ст. 53 Федерального закона от 29.12.2012 № 273-ФЗ «Об образовании в Российской Федерации», Положением об оказании платных дополнительных образовательных услуг в государственном 
автономном образовательном учреждении Астраханской области дополнительного образования «Региональный школьный технопарк», на основании договоров об оказании дополнительных платных образовательных услуг и представленных документов';
        $section->addText($text, null, array('align' => 'both'));
        $section->addText('ПРИКАЗЫВАЮ:');
        $section->addText('1.   Зачислить обучающихся согласно Приложению к настоящему приказу.');

        $petrovich = new Petrovich();
        $text = '';
        $text2 = '';
        foreach ($groups as $group)
        {
            $teacherTrG = $teacher->where(['training_group_id' => $group->training_group_id])->one();
            $temp = $petrovich->lastname($teacherTrG->teacherWork->secondname, Petrovich::CASE_ACCUSATIVE).' '.mb_substr($teacherTrG->teacherWork->firstname, 0, 1).'.'.mb_substr($teacherTrG->teacherWork->patronymic, 0, 1).'., ';
            if (strpos($text, $temp) === false)
            {
                $text .= $petrovich->lastname($teacherTrG->teacherWork->secondname, Petrovich::CASE_ACCUSATIVE).' '.mb_substr($teacherTrG->teacherWork->firstname, 0, 1).'.'.mb_substr($teacherTrG->teacherWork->patronymic, 0, 1).'., ';
            }
        }
        $text2 .= $petrovich->lastname($order->executor->secondname, Petrovich::CASE_INSTRUMENTAL).' '. mb_substr($order->executor->firstname, 0, 1).'. '.mb_substr($order->executor->patronymic, 0, 1).'. ';
        $section->addText('2. Назначить ' . $text . 'руководителем учебной группы, указанной в Приложении к настоящему приказу.', null, array('align' => 'both'));
        $section->addText('3. ' . $text2 . 'обеспечить:', null, array('align' => 'both'));
        $section->addText('        3.1. своевременное ознакомление руководителя учебной группы с', null, array('align' => 'both'));
        $section->addText('        настоящим приказом;', null, array('align' => 'both'));
        $section->addText('        3.2. контроль за соблюдением расписания занятий и соответствии ', null, array('align' => 'both'));
        $section->addText('        тематике проводимых учебных занятий дополнительной', null, array('align' => 'both'));
        $section->addText('        общеразвивающей программы – постоянно.', null, array('align' => 'both'));
        $section->addText('4. Руководителю учебной группы проводить с обучающимися инструктажи по технике безопасности в соответствии с дополнительной общеразвивающей программой.', null, array('align' => 'both'));
        $section->addText('5. Контроль за исполнением приказа оставляю за собой.', null, array('align' => 'both'));
        $section->addTextBreak(2);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Директор');
        $cell = $table->addCell(12000);
        $cell->addText('В.В. Войков', null, array('align' => 'right'));
        $section->addTextBreak(1);


        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Проект вносит:');
        $cell = $table->addCell(12000);
        $cell->addText(mb_substr($order->bring->firstname, 0, 1).'. '.mb_substr($order->bring->patronymic, 0, 1).'. '.$order->bring->secondname, null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Исполнитель:');
        $cell = $table->addCell(12000);
        $cell->addText(mb_substr($order->executor->firstname, 0, 1).'. '.mb_substr($order->executor->patronymic, 0, 1).'. '.$order->executor->secondname, null, array('align' => 'right'));
        $section->addTextBreak(1);
        $section->addText('Ознакомлены:');
        $table = $section->addTable();
        for ($i = 0; $i != count($res); $i++, $c++)
        {
            $fio = mb_substr($res[$i]->people->firstname, 0, 1) .'. '. mb_substr($res[$i]->people->patronymic, 0, 1) .'. '. $res[$i]->people->secondname;

            $table->addRow();
            $cell = $table->addCell(8000);
            $cell->addText('«___» __________ 20___ г.');
            $cell = $table->addCell(5000);
            $cell->addText('    ________________/', null, array('align' => 'right'));
            $cell = $table->addCell(5000);
            $cell->addText($fio . '/');
        }


        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(8000);
        $cell->addTextBreak(1);
        $cell = $table->addCell(10000);
        $cell->addText('Приложение к приказу ГАОУ АО ДО «РШТ»', null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(8000);
        $cell->addTextBreak(1);
        $cell = $table->addCell(10000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от ' . date("d", strtotime($order->order_date)) . ' '
                . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
                . date("Y", strtotime($order->order_date)) . ' г. '
                . $text);
        $section->addTextBreak(2);

        foreach ($groups as $group)
        {
            $trGroup = $trG->where(['id' => $group->training_group_id])->one();
            $section->addText('Учебная группа: ' . $trGroup->number);

            $teacherTrG = $teacher->where(['training_group_id' => $group->training_group_id])->one();
            $section->addText('Руководитель: ' . $teacherTrG->teacherWork->shortName);

            $programTrG = $program->where(['id' => $trGroup->training_program_id])->one();
            $section->addText('Дополнительная общеразвивающая программа: ' . $programTrG->name);
            $section->addText('Направленность: ' . $programTrG->stringFocus);

            $out = '';
            if ($programTrG->allow_remote == 0) $out = 'Только очная форма';
            if ($programTrG->allow_remote == 1) $out = 'Очная форма, с применением дистанционных технологий';
            if ($programTrG->allow_remote == 2) $out = 'Только дистанционная форма';
            $section->addText('Форма обучения: ' . $out);

            $section->addText('Срок освоения: ' . $programTrG->capacity . ' академ. ч.');
            $section->addText('Дата зачисления: ' . date("d", strtotime($order->order_date)) . ' '
                                                        . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
                                                        . date("Y", strtotime($order->order_date)) . ' г. ');
            $section->addText('Обучающиеся: ');
            $pasta = $pastaAlDente->where(['order_group_id' => $group->id])->all();
            for ($i = 0; $i < count($pasta); $i++)
            {
                $groupParticipant = $gPart->where(['id' => $pasta[$i]->group_participant_id])->one();
                $participant = $part->where(['id' => $groupParticipant->participant_id])->one();
                $section->addText($i+1 . '. ' . $participant->getFullName());
            }
            $section->addTextBreak(2);
        }

        $text = 'Пр.' . date("Ymd", strtotime($order->order_date)) . '_' . $order->order_number . $order->order_copy_id . $order->order_postfix . '_' . substr($order->order_name, 0, 20);
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $text . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($inputData, 'Word2007');
        $writer->save("php://output");
        exit;
    }

    static public function Deduction ($order_id) {
        ini_set('memory_limit', '512M');

        $inputData = new PhpWord();
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addText('Региональный', array('name' => 'Calibri', 'size' => '16'));
        $cell = $table->addCell(2000, array('borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText(' школьный', array('name' => 'Calibri', 'size' => '16'));
        $cell = $table->addCell(22000, array('valign' => 'bottom', 'borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText('  414000, г. Астрахань, ул. Адмиралтейская, д. 21, помещение № 66', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addImage(Yii::$app->basePath.'/templates/logo.png', array('width'=>100, 'height'=>40, 'align'=>'left'));
        $cell = $table->addCell(2000, array('valign' => 'top'));
        $cell->addText('технопарк', array('name' => 'Calibri', 'size' => '16'), array('align' => 'center'));
        $cell = $table->addCell(22000);
        $cell->addText(' +7 8512 442428 • info@schooltech.ru• www.школьныйтехнопарк.рф', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right'));
        //----------
        $section->addTextBreak(2);
        $section->addText('ПРИКАЗ', array('bold' => true), array('align' => 'center'));
        $section->addTextBreak(1);

        /*----------------*/
        $order = DocumentOrderWork::find()->where(['id' => $order_id])->one();
        $groups = OrderGroupWork::find()->where(['document_order_id' => $order->id])->all();
        $pastaAlDente = OrderGroupParticipantWork::find();
        $program = TrainingProgramWork::find();
        $teacher = TeacherGroupWork::find();
        $trG = TrainingGroupWork::find();
        $part = ForeignEventParticipantsWork::find();
        $gPart = TrainingGroupParticipantWork::find();
        $res = ResponsibleWork::find()->where(['document_order_id' => $order->id])->all();

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('«' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г.');
        $cell = $table->addCell(12000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText($text, null, array('align' => 'right'));
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText($order->order_name, null, array('align' => 'left'));
        $cell = $table->addCell(6000);
        $cell->addTextBreak(1);

        $section->addTextBreak(1);
        $text = '          В связи с досрочным прекращением образовательных отношений на основании статьи 61 Федерального закона от 29.12.2012 № 273-ФЗ 
«Об образовании в Российской Федерации» и заявлений родителей или законных представителей';
        $section->addText($text, null, array('align' => 'both'));
        $section->addText('ПРИКАЗЫВАЮ:');
        $section->addText('1.   Расторгнуть договора об оказании платных дополнительных образовательных услуг согласно Приложению № 1.', null, array('align' => 'both'));
        $section->addText('2.   Отчислить обучающихся согласно Приложению № 2 к настоящему приказу.', null, array('align' => 'both'));
        $section->addText('3.   Выдать обучающимся, указанным в Приложении № 2 к настоящему приказу, справки об обучении в ГАОУ АО ДО «РШТ» установленного образца.', null, array('align' => 'both'));
        $section->addText('4.   Контроль за исполнением приказа оставляю за собой.', null, array('align' => 'both'));
        $section->addTextBreak(2);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Директор');
        $cell = $table->addCell(12000);
        $cell->addText('В.В. Войков', null, array('align' => 'right'));
        $section->addTextBreak(1);


        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Проект вносит:');
        $cell = $table->addCell(12000);
        $cell->addText(mb_substr($order->bring->firstname, 0, 1).'. '.mb_substr($order->bring->patronymic, 0, 1).'. '.$order->bring->secondname, null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Исполнитель:');
        $cell = $table->addCell(12000);
        $cell->addText(mb_substr($order->executor->firstname, 0, 1).'. '.mb_substr($order->executor->patronymic, 0, 1).'. '.$order->executor->secondname, null, array('align' => 'right'));
        $section->addTextBreak(1);
        $section->addText('Ознакомлены:');
        $table = $section->addTable();
        for ($i = 0; $i != count($res); $i++, $c++)
        {
            $fio = mb_substr($res[$i]->people->firstname, 0, 1) .'. '. mb_substr($res[$i]->people->patronymic, 0, 1) .'. '. $res[$i]->people->secondname;

            $table->addRow();
            $cell = $table->addCell(8000);
            $cell->addText('«___» __________ 20___ г.');
            $cell = $table->addCell(5000);
            $cell->addText('    ________________/', null, array('align' => 'right'));
            $cell = $table->addCell(5000);
            $cell->addText($fio . '/');
        }


        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(8000);
        $cell->addTextBreak(1);
        $cell = $table->addCell(10000);
        $cell->addText('Приложение к приказу ГАОУ АО ДО «РШТ»', null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(8000);
        $cell->addTextBreak(1);
        $cell = $table->addCell(10000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от ' . date("d", strtotime($order->order_date)) . ' '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text);
        $section->addTextBreak(2);
        $section->addText('Договор об оказании платных дополнительных образовательных услуг от _____________ № __________.', null, array('align' => 'both'));


        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(8000);
        $cell->addTextBreak(1);
        $cell = $table->addCell(10000);
        $cell->addText('Приложение к приказу ГАОУ АО ДО «РШТ»', null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(8000);
        $cell->addTextBreak(1);
        $cell = $table->addCell(10000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от ' . date("d", strtotime($order->order_date)) . ' '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text);
        $section->addTextBreak(2);

        foreach ($groups as $group)
        {
            $trGroup = $trG->where(['id' => $group->training_group_id])->one();
            $section->addText('Учебная группа: ' . $trGroup->number);

            $teacherTrG = $teacher->where(['training_group_id' => $group->training_group_id])->one();
            $section->addText('Руководитель: ' . $teacherTrG->teacherWork->shortName);

            $programTrG = $program->where(['id' => $trGroup->training_program_id])->one();
            $section->addText('Дополнительная общеразвивающая программа: ' . $programTrG->name);
            $section->addText('Направленность: ' . $programTrG->stringFocus);

            $out = '';
            if ($programTrG->allow_remote == 0) $out = 'Только очная форма';
            if ($programTrG->allow_remote == 1) $out = 'Очная форма, с применением дистанционных технологий';
            if ($programTrG->allow_remote == 2) $out = 'Только дистанционная форма';
            $section->addText('Форма обучения: ' . $out);

            $section->addText('Срок освоения: ' . $programTrG->capacity . ' академ. ч.');
            $section->addText('Обучающиеся: ');
            $pasta = $pastaAlDente->where(['order_group_id' => $group->id])->all();
            for ($i = 0; $i < count($pasta); $i++)
            {
                $groupParticipant = $gPart->where(['id' => $pasta[$i]->group_participant_id])->one();
                $participant = $part->where(['id' => $groupParticipant->participant_id])->one();
                $section->addText($i+1 . '. ' . $participant->getFullName());
            }
            $section->addTextBreak(2);
        }

        $text = 'Пр.' . date("Ymd", strtotime($order->order_date)) . '_' . $order->order_number . $order->order_copy_id . $order->order_postfix . '_' . substr($order->order_name, 0, 20);
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $text . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($inputData, 'Word2007');
        $writer->save("php://output");
        exit;
    }

    static public function DeductionAll ($order_id) {
        ini_set('memory_limit', '512M');

        $inputData = new PhpWord();
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addText('Региональный', array('name' => 'Calibri', 'size' => '16'));
        $cell = $table->addCell(2000, array('borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText(' школьный', array('name' => 'Calibri', 'size' => '16'));
        $cell = $table->addCell(22000, array('valign' => 'bottom', 'borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText('  414000, г. Астрахань, ул. Адмиралтейская, д. 21, помещение № 66', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addImage(Yii::$app->basePath.'/templates/logo.png', array('width'=>100, 'height'=>40, 'align'=>'left'));
        $cell = $table->addCell(2000, array('valign' => 'top'));
        $cell->addText('технопарк', array('name' => 'Calibri', 'size' => '16'), array('align' => 'center'));
        $cell = $table->addCell(22000);
        $cell->addText(' +7 8512 442428 • info@schooltech.ru• www.школьныйтехнопарк.рф', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right'));
        //----------
        $section->addTextBreak(2);
        $section->addText('ПРИКАЗ', array('bold' => true), array('align' => 'center'));
        $section->addTextBreak(1);

        /*----------------*/
        $order = DocumentOrderWork::find()->where(['id' => $order_id])->one();
        $groups = OrderGroupWork::find()->where(['document_order_id' => $order->id])->all();
        $pastaAlDente = OrderGroupParticipantWork::find();
        $program = TrainingProgramWork::find();
        $teacher = TeacherGroupWork::find();
        $trG = TrainingGroupWork::find();
        $part = ForeignEventParticipantsWork::find();
        $gPart = TrainingGroupParticipantWork::find();
        $res = ResponsibleWork::find()->where(['document_order_id' => $order->id])->all();

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('«' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г.');
        $cell = $table->addCell(12000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText($text, null, array('align' => 'right'));
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText($order->order_name, null, array('align' => 'left'));
        $cell = $table->addCell(6000);
        $cell->addTextBreak(1);

        $section->addTextBreak(1);
        $section->addText('          В связи с завершением обучения в ГАОУ АО ДО «РШТ», на основании решения аттестационной комиссии от ____________.', null, array('align' => 'both'));
        $section->addText('ПРИКАЗЫВАЮ:');
        $section->addText('1.   Отчислить обучающихся согласно Приложению к настоящему приказу.', null, array('align' => 'both'));
        $section->addText('2.   Выдать обучающимся, указанным в Приложении к настоящему приказу, сертификаты об успешном завершении обучения.', null, array('align' => 'both'));
        $section->addText('3.   Контроль за исполнением приказа оставляю за собой.', null, array('align' => 'both'));
        $section->addTextBreak(2);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Директор');
        $cell = $table->addCell(12000);
        $cell->addText('В.В. Войков', null, array('align' => 'right'));
        $section->addTextBreak(1);


        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Проект вносит:');
        $cell = $table->addCell(12000);
        $cell->addText(mb_substr($order->bring->firstname, 0, 1).'. '.mb_substr($order->bring->patronymic, 0, 1).'. '.$order->bring->secondname, null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Исполнитель:');
        $cell = $table->addCell(12000);
        $cell->addText(mb_substr($order->executor->firstname, 0, 1).'. '.mb_substr($order->executor->patronymic, 0, 1).'. '.$order->executor->secondname, null, array('align' => 'right'));
        $section->addTextBreak(1);
        $section->addText('Ознакомлены:');
        $table = $section->addTable();
        for ($i = 0; $i != count($res); $i++, $c++)
        {
            $fio = mb_substr($res[$i]->people->firstname, 0, 1) .'. '. mb_substr($res[$i]->people->patronymic, 0, 1) .'. '. $res[$i]->people->secondname;

            $table->addRow();
            $cell = $table->addCell(8000);
            $cell->addText('«___» __________ 20___ г.');
            $cell = $table->addCell(5000);
            $cell->addText('    ________________/', null, array('align' => 'right'));
            $cell = $table->addCell(5000);
            $cell->addText($fio . '/');
        }

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(8000);
        $cell->addTextBreak(1);
        $cell = $table->addCell(10000);
        $cell->addText('Приложение к приказу ГАОУ АО ДО «РШТ»', null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(8000);
        $cell->addTextBreak(1);
        $cell = $table->addCell(10000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от ' . date("d", strtotime($order->order_date)) . ' '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text);
        $section->addTextBreak(2);

        foreach ($groups as $group)
        {
            $trGroup = $trG->where(['id' => $group->training_group_id])->one();
            $section->addText('Учебная группа: ' . $trGroup->number);

            $teacherTrG = $teacher->where(['training_group_id' => $group->training_group_id])->one();
            $section->addText('Руководитель: ' . $teacherTrG->teacherWork->shortName);

            $programTrG = $program->where(['id' => $trGroup->training_program_id])->one();
            $section->addText('Дополнительная общеразвивающая программа: ' . $programTrG->name);
            $section->addText('Направленность: ' . $programTrG->stringFocus);

            $out = '';
            if ($programTrG->allow_remote == 0) $out = 'Только очная форма';
            if ($programTrG->allow_remote == 1) $out = 'Очная форма, с применением дистанционных технологий';
            if ($programTrG->allow_remote == 2) $out = 'Только дистанционная форма';
            $section->addText('Форма обучения: ' . $out);

            $section->addText('Срок освоения: ' . $programTrG->capacity . ' академ. ч.');
            $section->addText('Обучающиеся: ');
            $pasta = $pastaAlDente->where(['order_group_id' => $group->id])->all();
            for ($i = 0; $i < count($pasta); $i++)
            {
                $groupParticipant = $gPart->where(['id' => $pasta[$i]->group_participant_id])->one();
                $participant = $part->where(['id' => $groupParticipant->participant_id])->one();
                $section->addText($i+1 . '. ' . $participant->getFullName());
            }
            $section->addTextBreak(2);
        }

        $text = 'Пр.' . date("Ymd", strtotime($order->order_date)) . '_' . $order->order_number . $order->order_copy_id . $order->order_postfix . '_' . substr($order->order_name, 0, 20);
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $text . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($inputData, 'Word2007');
        $writer->save("php://output");
        exit;
    }

    static public function Transfer ($order_id) {
        ini_set('memory_limit', '512M');

        $inputData = new PhpWord();
        //$reader = \PhpOffice\PhpWord\IOFactory::createReader('Word2007');
        //$inputData = $reader->load(Yii::$app->basePath.'\templates\order_study.docx');
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addText('Региональный', array('name' => 'Calibri', 'size' => '16'));
        $cell = $table->addCell(2000, array('borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText(' школьный', array('name' => 'Calibri', 'size' => '16'));
        $cell = $table->addCell(22000, array('valign' => 'bottom', 'borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText('  414000, г. Астрахань, ул. Адмиралтейская, д. 21, помещение № 66', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addImage(Yii::$app->basePath.'/templates/logo.png', array('width'=>100, 'height'=>40, 'align'=>'left'));
        $cell = $table->addCell(2000, array('valign' => 'top'));
        $cell->addText('технопарк', array('name' => 'Calibri', 'size' => '16'), array('align' => 'center'));
        $cell = $table->addCell(22000);
        $cell->addText(' +7 8512 442428 • info@schooltech.ru• www.школьныйтехнопарк.рф', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right'));
        //----------
        $section->addTextBreak(2);
        $section->addText('ПРИКАЗ', array('bold' => true), array('align' => 'center'));
        $section->addTextBreak(1);

        /*----------------*/
        $order = DocumentOrderWork::find()->where(['id' => $order_id])->one();
        $groups = OrderGroupWork::find()->where(['document_order_id' => $order->id])->all();
        $pastaAlDente = OrderGroupParticipantWork::find();
        $program = TrainingProgramWork::find();
        $trG = TrainingGroupWork::find();
        $part = ForeignEventParticipantsWork::find();
        $gPart = TrainingGroupParticipantWork::find();

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('«' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г.');
        $cell = $table->addCell(12000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText($text, null, array('align' => 'right'));
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText($order->order_name, null, array('align' => 'left'));
        $cell = $table->addCell(6000);
        $cell->addTextBreak(1);

        $section->addTextBreak(1);
        $text = '          На   основании   заявления   родителя (законного представителя) _____________________________ от ______________, в соответствии с п. 2.1.3 
Положения о порядке и основаниях перевода, отчисления и восстановления обучающихся государственного автономного образовательного учреждения Астраханской области 
дополнительного образования «Региональный школьный технопарк»';
        $section->addText($text, null, array('align' => 'both'));
        $section->addText('          ПРИКАЗЫВАЮ:');

        $petrovich = new Petrovich();
        $name = '';
        $oldGr = '';
        $newGr = '';
        foreach ($groups as $group)
        {
            $pasta = $pastaAlDente->where(['order_group_id' => $group->id])->all();
            foreach ($pasta as $macaroni)
            {
                $groupParticipant = $gPart->where(['id' => $macaroni->group_participant_id])->one();
                $participant = $part->where(['id' => $groupParticipant->participant_id])->one();
                $temp = $petrovich->lastname($participant->secondname, Petrovich::CASE_ACCUSATIVE) . ' ' .
                    $petrovich->firstname($participant->firstname, Petrovich::CASE_ACCUSATIVE) . ' ' .
                    $petrovich->firstname($participant->patronymic,Petrovich::CASE_ACCUSATIVE) . ' и ';
                if (strpos($name, $temp) === false)
                    $name .= $temp;
                if ($macaroni->status === 2)
                    $oldGr = $trG->where(['id' => $groupParticipant->training_group_id])->one();
                else
                    $newGr = $trG->where(['id' => $groupParticipant->training_group_id])->one();
            }
            $name = mb_substr($name, 0, mb_strlen($name) - 2, "utf-8");
        }
        $programTrG = $program->where(['id' => $oldGr->training_program_id])->one();

        $section->addText('          1.   Перевести обучающегося ' . $name . 'из учебной группы '. $oldGr->number . ' в учебную группу ' .
            $newGr->number .  ' в рамках обучения по дополнительной общеразвивающей программе ' .
            mb_substr(mb_strtolower($programTrG->stringFocus), 0, mb_strlen($programTrG->stringFocus) - 2, "utf-8")
             . 'ой направленности «' . $programTrG->name . '».',null, array('align' => 'both'));
        $section->addText('          2.   Контроль за исполнением приказа оставляю за собой.', null, array('align' => 'both'));
        $section->addTextBreak(2);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Директор');
        $cell = $table->addCell(12000);
        $cell->addText('В.В. Войков', null, array('align' => 'right'));
        $section->addTextBreak(1);

        $text = 'Пр.' . date("Ymd", strtotime($order->order_date)) . '_' . $order->order_number . $order->order_copy_id . $order->order_postfix . '_' . substr($order->order_name, 0, 20);
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $text . '.docx"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');

        $writer = \PhpOffice\PhpWord\IOFactory::createWriter($inputData, 'Word2007');
        $writer->save("php://output");
        exit;
    }
}