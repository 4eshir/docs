<?php

//--g
namespace app\models\components;

use app\models\extended\AccessTrainingGroup;
use app\models\work\DocumentOrderWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\OrderGroupParticipantWork;
use app\models\work\OrderGroupWork;
use app\models\components\petrovich\Petrovich;
use app\models\work\PeoplePositionBranchWork;
use app\models\work\PositionWork;
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
        $section->addTextBreak(1);
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
        $pos = PeoplePositionBranchWork::find();
        $positionName = PositionWork::find();

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

        //$section->addTextBreak(1);
        if ($trG->where(['id' => $groups[0]->training_group_id])->one()->budget === 1)
            $text = '<w:br/>          В соответствии с ч. 1 ст. 53 Федерального закона от 29.12.2012                    № 273-ФЗ «Об образовании в Российской Федерации», Правилами приема обучающихся в государственное автономное образовательное учреждение Астраханской области дополнительного образования «Региональный школьный технопарк» на обучение по дополнительным общеразвивающим программам, на основании заявлений о приеме на обучение по дополнительным общеразвивающим программам';
        else
            $text = '<w:br/>          В соответствии с ч. 1, ч. 2 ст. 53 Федерального закона от 29.12.2012                    № 273-ФЗ «Об образовании в Российской Федерации», Положением об оказании платных дополнительных образовательных услуг в государственном автономном образовательном учреждении Астраханской области дополнительного образования «Региональный школьный технопарк», на основании договоров об оказании дополнительных платных образовательных услуг и представленных документов';
        $text .= '<w:br/>          ПРИКАЗЫВАЮ:';
        $text .= '<w:br/>          1.	Зачислить обучающихся с «' . date("d", strtotime($order->order_date)) . '» ' . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г.' . ' в учебные группы ГАОУ АО ДО «РШТ» на обучение по дополнительным общеразвивающим программам согласно Приложению к настоящему приказу.';

        $countTeacher = 0;
        if (count($groups) == 1) {
            $countTeacher = count($teacher->where(['training_group_id' => $groups[0]->training_group_id])->all());
            if ($countTeacher == 1)
                $text .= '<w:br/>          2.	Назначить руководителем учебной группы работника ГАОУ АО ДО «РШТ», указанного в Приложении к настоящему приказу.';
            else
            {
                $text .= '<w:br/>          2.	Назначить руководителями учебной группы работников ГАОУ АО ДО «РШТ», указанных в Приложении к настоящему приказу.';
            }
        }
        else
        {
            $teachers = [];
            foreach ($groups as $group)
            {
                $trGs = $teacher->where(['training_group_id' => $group->training_group_id])->all();
                foreach ($trGs as $trGr)
                {
                    $teachers [] = $trGr->teacher_id;
                }
            }
            $countTeacher = count(array_unique($teachers));

            if ($countTeacher == 1)
                $text .= '<w:br/>          2.	Назначить руководителем учебных групп работника ГАОУ АО ДО «РШТ», указанного в Приложении к настоящему приказу.';
            else
                $text .= '<w:br/>          2.	Назначить руководителями учебных групп работников ГАОУ АО ДО «РШТ», указанных в Приложении к настоящему приказу.';
        }

        $posOne = $pos->where(['people_id' => $order->executor_id])->one();
        $text .= '<w:br/>          3.	Ответственным за контроль соблюдения расписания учебных групп и соответствия тематике проводимых учебных занятий';
        if (count($groups) == 1)
            $text .= ' дополнительной общеразвивающей программе назначить работника: ';
        else
            $text .= 'дополнительным общеразвивающим программам назначить работника: ';
        $text .= mb_strtolower($posOne->position->name) . ' ' . mb_substr($order->executor->firstname, 0, 1) . '. ' . mb_substr($order->executor->patronymic, 0, 1) . '. ' . $order->executor->secondname;

        if ($countTeacher === 1)
            $text .= '<w:br/>          4.	Руководителем ';
        else
            $text .= '<w:br/>          4.	Руководителям ';
        if (count($groups) == 1)
            $text .= 'учебной группы ';
        else
            $text .= 'учебных групп ';
        $text .= 'проводить с обучающимися инструктажи по технике безопасности в соответствии с дополнительными общеразвивающими программами.';

        $posOne = $pos->where(['people_id' => $order->bring_id])->one();
        $text .= '<w:br/>          5.	Ответственным за своевременное ознакомление руководителей учебных групп с настоящим приказом назначить работника: '
            . mb_strtolower($posOne->position->name) . ' ' . mb_substr($order->bring->firstname, 0, 1) . '. ' . mb_substr($order->bring->patronymic, 0, 1) . '. ' . $order->bring->secondname;

        $text .= '<w:br/>          6.	Контроль исполнения приказа оставляю за собой.';


        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both'));
        $section->addTextBreak(1);

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
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
                . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
                . date("Y", strtotime($order->order_date)) . ' г. '
                . $text);
        $section->addTextBreak(2);

        foreach ($groups as $group)
        {
            $trGroup = $trG->where(['id' => $group->training_group_id])->one();
            $section->addText('Учебная группа: ' . $trGroup->number);

            $teacherTrG = $teacher->where(['training_group_id' => $group->training_group_id])->all();
            $text = 'Руководитель: ';

            foreach ($teacherTrG as $trg)
            {
                $post = [];
                $pPosB = $pos->where(['people_id' => $trg->teacher_id])->all();
                foreach ($pPosB as $posOne)
                {
                    $post [] = $posOne->position_id;
                }
                $post = array_unique($post);    // выкинули все повторы
                $post = array_intersect($post, [15, 16, 35, 44]);   // оставили только преподские должности

                if (count($post) > 0)
                {
                    $posName = $positionName->where(['id' => $post[0]])->one();
                    $text .= mb_strtolower($posName->name) . ' ' . $trg->teacherWork->shortName . ', ';
                }
                else
                    $text .= $trg->teacherWork->shortName . ', ';
            }
            $text = mb_substr($text, 0, -2);
            $section->addText($text);

            $programTrG = $program->where(['id' => $trGroup->training_program_id])->one();
            $section->addText('Дополнительная общеразвивающая программа: ' . $programTrG->name);
            $section->addText('Направленность: ' . $programTrG->stringFocus);

            $section->addText('Форма обучения: очная (в случаях, установленных законодательными актами, возможно применение электронного обучения с дистанционными образовательными технологиями).');

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

        $text = 'Пр.' . date("Ymd", strtotime($order->order_date)) . '_' . $order->order_number . $order->order_copy_id . $order->order_postfix . '_' . mb_substr($order->order_name, 0, 20);
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
        $section->addTextBreak(1);
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
        $pos = PeoplePositionBranchWork::find();
        $positionName = PositionWork::find();

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

        $countPasta = count($pastaAlDente->joinWith(['orderGroup orderGroup'])->where(['orderGroup.document_order_id' => $order->id])->all());

        if ($order->study_type == 0)
            $text = '          В связи с завершением обучения в ГАОУ АО ДО «РШТ», на основании решения аттестационной комиссии/ протоколов жюри/ судейской коллегии/ итоговой диагностической карты от ' .
                '«' . date("d", strtotime($order->order_date)) . '» ' . WordWizard::Month(date("m", strtotime($order->order_date))) . ' ' . date("Y", strtotime($order->order_date)) . ' г.';
        else if ($order->study_type == 1)
            $text = '          В связи с завершением обучения в ГАОУ АО ДО «РШТ»';
        else if ($order->study_type == 2)
        {
            $text = '          В связи с досрочным прекращением образовательных отношений на основании статьи 61 Федерального закона от 29.12.2012 № 273-ФЗ «Об образовании в Российской Федерации» и ';
            if ($countPasta > 1)
                $text .= 'заявлений родителей или законных представителей,   ';
            else
                $text .= 'заявления родителя или законного представителя,   ';
        }
        else
            $text = '          В связи с досрочным прекращением образовательных отношений на основании статьи 61 Федерального закона от 29.12.2012 № 273-ФЗ «Об образовании в Российской Федерации», п. 6.2.3 договоров об оказании платных дополнительных образовательных услуг,  ';

        $text .= '<w:br/>          ПРИКАЗЫВАЮ:';

        if ($order->study_type == 0 && $countPasta > 1)
            $text .= '<w:br/>          1.	Отчислить обучающихся согласно Приложению к настоящему приказу.<w:br/>          2.	Выдать обучающимся, указанным в Приложении к настоящему приказу, сертификаты об успешном завершении обучения.<w:br/>          3.	Контроль исполнения приказа оставляю за собой.';
        if ($order->study_type == 0 && $countPasta == 1)
            $text .= '<w:br/>          1.	Отчислить обучающегося согласно Приложению к настоящему приказу.<w:br/>          2.	Выдать обучающемуся, указанному в Приложении к настоящему приказу, сертификат об успешном завершении обучения.<w:br/>          3.	Контроль исполнения приказа оставляю за собой.';

        if ($order->study_type == 1 && $countPasta > 1)
            $text .= '<w:br/>          1.	Отчислить обучающихся согласно Приложению к настоящему приказу.<w:br/>          2.	Выдать обучающимся, не прошедшим итоговую форму контроля и указанным в Приложении к настоящему приказу, справки об обучении в ГАОУ АО ДО «РШТ» установленного учреждением образца.<w:br/>          3.	Контроль исполнения приказа оставляю за собой.';
        if ($order->study_type == 1 && $countPasta == 1)
            $text .= '<w:br/>          1.	Отчислить обучающегося согласно Приложению к настоящему приказу.<w:br/>          2.	Выдать обучающемуся, не прошедшему итоговую форму контроля и указанному в Приложении к настоящему приказу, справку об обучении в ГАОУ АО ДО «РШТ» установленного учреждением образца.<w:br/>          3.	Контроль исполнения приказа оставляю за собой.';

        if ($order->study_type == 2)
        {
            if ($trG->where(['id' => $groups[0]->training_group_id])->one()->budget === 1)
            {
                if ($countPasta > 1)
                    $text .= '<w:br/>          1.	Отчислить обучающихся согласно Приложению к настоящему приказу.<w:br/>          2.	Выдать обучающимся, указанным в Приложении к настоящему приказу, справки об обучении в ГАОУ АО ДО «РШТ» установленного учреждением образца.<w:br/>          3.	Контроль исполнения приказа оставляю за собой.';
                else
                    $text .= '<w:br/>          1.	Отчислить обучающегося согласно Приложению к настоящему приказу.<w:br/>          2.	Выдать обучающемуся, указанному в Приложении к настоящему приказу, справку об обучении в ГАОУ АО ДО «РШТ» установленного учреждением образца.<w:br/>          3.	Контроль исполнения приказа оставляю за собой.';
            }
            else
            {
                if ($countPasta > 1)
                    $text .= '<w:br/>          1.	Расторгнуть договора об оказании платных дополнительных образовательных услуг согласно Приложению № 1.<w:br/>          2.	Отчислить обучающихся согласно Приложению № 2 к настоящему приказу.<w:br/>          3.	Выдать обучающимся, указанным в Приложении № 2 к настоящему приказу, справки об обучении в ГАОУ АО ДО «РШТ» установленного образца.<w:br/>          4.	Контроль исполнения приказа оставляю за собой.';
                else
                    $text .= '<w:br/>          1.	Расторгнуть договор об оказании платных дополнительных образовательных услуг согласно Приложению № 1.<w:br/>          2.	Отчислить обучающегося согласно Приложению № 2 к настоящему приказу.<w:br/>          3.	Выдать обучающемуся, указанному в Приложении № 2 к настоящему приказу, справку об обучении в ГАОУ АО ДО «РШТ» установленного образца.<w:br/>          4.	Контроль исполнения приказа оставляю за собой.';
            }
        }

        if ($order->study_type == 3 && $countPasta > 1)
            $text .= '<w:br/>          1.	Расторгнуть договора об оказании платных дополнительных образовательных услуг согласно Приложению № 1.<w:br/>          2.	Отчислить обучающихся согласно Приложению № 2 к настоящему приказу. <w:br/>          3.	Выдать обучающимся, указанным в Приложении № 2 к настоящему приказу, справки об обучении в ГАОУ АО ДО «РШТ» установленного образца. <w:br/>          4.	Контроль исполнения приказа оставляю за собой.';
        if ($order->study_type == 3 && $countPasta == 1)
            $text .= '<w:br/>          1.	Расторгнуть договор об оказании платных дополнительных образовательных услуг согласно Приложению № 1.<w:br/>          2.	Отчислить обучающегося согласно Приложению № 2 к настоящему приказу. <w:br/>          3.	Выдать обучающемуся, указанному в Приложении № 2 к настоящему приказу, справку об обучении в ГАОУ АО ДО «РШТ» установленного образца. <w:br/>          4.	Контроль исполнения приказа оставляю за собой.';

        $section->addText($text, null, array('align' => 'both'));


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

        if (($order->study_type == 2 && $trG->where(['id' => $groups[0]->training_group_id])->one()->budget !== 1) || $order->study_type == 3)
        {
            $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
                'marginLeft' => WordWizard::convertMillimetersToTwips(30),
                'marginBottom' => WordWizard::convertMillimetersToTwips(20),
                'marginRight' => WordWizard::convertMillimetersToTwips(15)));
            $table = $section->addTable();
            $table->addRow();
            $cell = $table->addCell(6500);
            $cell->addTextBreak(1);
            $cell = $table->addCell(10900);
            $cell->addText('Приложение № 1 к приказу ГАОУ АО ДО «РШТ»');
            $table->addRow();
            $cell = $table->addCell(6500);
            $cell->addTextBreak(1);
            $cell = $table->addCell(10900);
            $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
            if ($order->order_postfix !== NULL)
                $text .= '/' . $order->order_postfix;
            $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
                . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
                . date("Y", strtotime($order->order_date)) . ' г. '
                . $text);
            $section->addTextBreak(2);

            $text = '';
            for ($i = 0; $i < $countPasta; $i++)
            {
                $text .= '<w:br/>' . ($i + 1) . '. Договор об оказании платных дополнительных образовательных услуг от __________ г. № ____.';
            }
            $section->addText($text, null, array('align' => 'both'));
        }

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6500);
        $cell->addTextBreak(1);
        $cell = $table->addCell(10900);
        if (($order->study_type == 2 && $trG->where(['id' => $groups[0]->training_group_id])->one()->budget !== 1) || $order->study_type == 3)
            $cell->addText('Приложение № 2 к приказу ГАОУ АО ДО «РШТ»');
        else
            $cell->addText('Приложение к приказу ГАОУ АО ДО «РШТ»', null, array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(6500);//8000 10000
        $cell->addTextBreak(1);
        $cell = $table->addCell(10900);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text);
        $section->addTextBreak(2);

        foreach ($groups as $group)
        {
            $trGroup = $trG->where(['id' => $group->training_group_id])->one();
            $section->addText('Учебная группа: ' . $trGroup->number);

            $teacherTrG = $teacher->where(['training_group_id' => $group->training_group_id])->all();
            $text = 'Руководитель: ';

            foreach ($teacherTrG as $trg)
            {
                $post = [];
                $pPosB = $pos->where(['people_id' => $trg->teacher_id])->all();
                foreach ($pPosB as $posOne)
                {
                    $post [] = $posOne->position_id;
                }
                $post = array_unique($post);    // выкинули все повторы
                $post = array_intersect($post, [15, 16, 35, 44]);   // оставили только преподские должности

                if (count($post) > 0)
                {
                    $posName = $positionName->where(['id' => $post[0]])->one();
                    $text .= mb_strtolower($posName->name) . ' ' . $trg->teacherWork->shortName . ', ';
                }
                else
                    $text .= $trg->teacherWork->shortName . ', ';
            }
            $text = mb_substr($text, 0, -2);
            $section->addText($text);

            $programTrG = $program->where(['id' => $trGroup->training_program_id])->one();
            $section->addText('Дополнительная общеразвивающая программа: ' . $programTrG->name);
            $section->addText('Направленность: ' . $programTrG->stringFocus);

            $section->addText('Форма обучения: очная (в случаях, установленных законодательными актами, возможно применение электронного обучения с дистанционными образовательными технологиями).');

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

        $text = 'Пр.' . date("Ymd", strtotime($order->order_date)) . '_' . $order->order_number . $order->order_copy_id . $order->order_postfix . '_' . mb_substr($order->order_name, 0, 20);
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

    static public function Transfer ($order_id)
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
            'marginRight' => WordWizard::convertMillimetersToTwips(15)));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addText('Региональный', array('name' => 'Calibri', 'size' => '16'));
        $cell = $table->addCell(2000, array('borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText(' школьный', array('name' => 'Calibri', 'size' => '16'));
        $cell = $table->addCell(22000, array('valign' => 'bottom', 'borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText('  414000, г. Астрахань, ул. Адмиралтейская, д. 21, помещение № 66', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array('align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addImage(Yii::$app->basePath . '/templates/logo.png', array('width' => 100, 'height' => 40, 'align' => 'left'));
        $cell = $table->addCell(2000, array('valign' => 'top'));
        $cell->addText('технопарк', array('name' => 'Calibri', 'size' => '16'), array('align' => 'center'));
        $cell = $table->addCell(22000);
        $cell->addText(' +7 8512 442428 • info@schooltech.ru• www.школьныйтехнопарк.рф', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array('align' => 'right'));
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
            $text .= '/' . $order->order_postfix;
        $cell->addText($text, null, array('align' => 'right'));
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText($order->order_name, null, array('align' => 'left'));
        $cell = $table->addCell(6000);
        $cell->addTextBreak(1);

        $pasta = $pastaAlDente->joinWith(['orderGroup orderGroup'])->where(['orderGroup.document_order_id' => $order->id])->andWhere(['status' => 0])->all();
        $countPasta = count($pasta);

        $section->addTextBreak(1);
        if ($order->study_type == 0)
            $text = '          На основании решения Педагогического совета ГАОУ АО ДО «РШТ» от «____»_________ 20___ г. № ______, в соответствии с п. 2.1.1 Положения о порядке и основаниях перевода, отчисления и восстановления обучающихся государственного автономного образовательного учреждения Астраханской области дополнительного образования «Региональный школьный технопарк»';
        if ($order->study_type == 1)
            $text = '          На основании заявления родителя (или законного представителя) _____________________________________________________ от ________ г., и решения Педагогического совета ГАОУ АО ДО «РШТ» от «____»_________ 20___ г. № ______, в соответствии с п. 2.1.2 Положения о порядке и основаниях перевода, отчисления и восстановления обучающихся государственного автономного образовательного учреждения Астраханской области дополнительного образования «Региональный школьный технопарк»';
        if ($order->study_type == 2)
            $text = '          На основании заявления родителя (или законного представителя) _____________________________________________________ от ________ г., в соответствии с п. 2.1.3 Положения о порядке и основаниях перевода, отчисления и восстановления обучающихся государственного автономного образовательного учреждения Астраханской области дополнительного образования «Региональный школьный технопарк»';

        $text .= '<w:br/>          ПРИКАЗЫВАЮ:';

        if ($order->study_type == 0)
        {
            if ($countPasta == 1)
                $text .= '<w:br/>          1. Перевести обучающегося, успешно прошедшего итоговую аттестацию, на следующий год обучения по дополнительной общеразвивающей программе согласно Приложению к настоящему приказу.';
            else if (count($groups) == 1)
                $text .= '<w:br/>          1. Перевести обучающихся, успешно прошедших итоговую аттестацию, на следующий год обучения по дополнительной общеразвивающей программе согласно Приложению к настоящему приказу.';
            else
                $text .= '<w:br/>          1. Перевести обучающихся, успешно прошедших итоговую аттестацию, на следующий год обучения по дополнительным общеразвивающим программам согласно Приложению к настоящему приказу.';
        }
        else
        {
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
                    $temp = $participant->getFullName() . ' и ';
                    if (strpos($name, $temp) === false)
                        $name .= $temp;
                    if ($macaroni->status === 2)
                        $oldGr = $trG->where(['id' => $groupParticipant->training_group_id])->one();
                    else
                        $newGr = $trG->where(['id' => $groupParticipant->training_group_id])->one();
                }
                $name = mb_substr($name, 0, mb_strlen($name) - 2, "utf-8");
            }
            $oldProgramTrG = $program->where(['id' => $oldGr->training_program_id])->one();
            $newProgramTrG = $program->where(['id' => $newGr->training_program_id])->one();

            if ($order->study_type == 1)
            {
                $text .= '<w:br/>          1. Перевести с обучения по дополнительной общеразвивающей программе «' . $oldProgramTrG->name . '» на обучение по дополнительной общеразвивающей программе «'
                    . $newProgramTrG->name . '» ';
            }

            if ($order->study_type == 2)
            {
                $text .= '<w:br/>          1. Перевести из учебной группы ' . $oldGr->number . ' в учебную группу '
                    . $newGr->number .  ' в рамках обучения по дополнительной общеразвивающей программе «' . $newProgramTrG->name . '», '
                    . mb_substr(mb_strtolower($newProgramTrG->stringFocus), 0, mb_strlen($newProgramTrG->stringFocus) - 2, "utf-8")
                    . 'ой направленности ';
            }

            if ($countPasta > 1)
                $text .= 'обучающихся: ' . $name . '.';
            else
                $text .= 'обучающегося: ' . $name . '.';
        }

        $text .= '<w:br/>          2. Контроль за исполнением приказа оставляю за собой.';

        $section->addText($text, null, array('align' => 'both'));
        $section->addTextBreak(2);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Директор');
        $cell = $table->addCell(12000);
        $cell->addText('В.В. Войков', null, array('align' => 'right'));
        $section->addTextBreak(1);

        if ($order->study_type == 0)
        {
            $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
                'marginLeft' => WordWizard::convertMillimetersToTwips(30),
                'marginBottom' => WordWizard::convertMillimetersToTwips(20),
                'marginRight' => WordWizard::convertMillimetersToTwips(15)));
            $table = $section->addTable();
            $table->addRow();
            $cell = $table->addCell(6500);
            $cell->addTextBreak(1);
            $cell = $table->addCell(10900);
            $cell->addText('Приложение к приказу ГАОУ АО ДО «РШТ»');
            $table->addRow();
            $cell = $table->addCell(6500);
            $cell->addTextBreak(1);
            $cell = $table->addCell(10900);
            $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
            if ($order->order_postfix !== NULL)
                $text .= '/' . $order->order_postfix;
            $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
                . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
                . date("Y", strtotime($order->order_date)) . ' г. '
                . $text);
            $section->addTextBreak(2);


            $newGroup = [];
            foreach ($pasta as $macaroni)
            {
                $groupPart = $gPart->where(['id' => $macaroni->group_participant_id])->one();
                $trGPart = $trG->where(['id' => $groupPart->training_group_id])->one();
                $programPart = $program->where(['id' => $trGPart->training_program_id])->one();
                $partName = $part->where(['id' => $groupPart->participant->id])->one();

                $newGroup[$programPart->name][$trGPart->number][] = $partName->getFullName();
            }

            for ($i = 0; $i < count($newGroup); $i++)
            {
                //$text = 'Дополнительная общеразвивающая программа: «' . key($newGroup) . '»';
                $section->addText('Дополнительная общеразвивающая программа: «' . key($newGroup) . '»', array('bold' => true), array('align' => 'both'));

                for ($j = 0; $j < count($newGroup[key($newGroup)]); $j++)
                {
                    //$text .= '<w:br/>Учебная группа: ' . key($newGroup[key($newGroup)]);
                    $section->addText('Учебная группа: ' . key($newGroup[key($newGroup)]), null, array('align' => 'both'));

                    for ($k = 0; $k < count($newGroup[key($newGroup)][key($newGroup[key($newGroup)])]); $k++)
                    {
                        //$text .= '<w:br/>   ' . ($k + 1) . '. ' . $newGroup[key($newGroup)][key($newGroup[key($newGroup)])][$k];
                        $section->addText('   ' . ($k + 1) . '. ' . $newGroup[key($newGroup)][key($newGroup[key($newGroup)])][$k] . ', 2-й год обучения', null, array('align' => 'both'));
                    }

                    next($newGroup[key($newGroup)]);
                }
                next($newGroup);
                //$section->addText($text, null, array('align' => 'both'));
                $section->addTextBreak(1);
            }
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
}