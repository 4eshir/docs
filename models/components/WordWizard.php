<?php

//--g
namespace app\models\components;

use app\models\extended\AccessTrainingGroup;
use app\models\work\DocumentOrderSupplementWork;
use app\models\work\DocumentOrderWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\ForeignEventWork;
use app\models\work\OrderGroupParticipantWork;
use app\models\work\OrderGroupWork;
use app\models\components\petrovich\Petrovich;
use app\models\work\PeoplePositionBranchWork;
use app\models\work\PositionWork;
use app\models\work\ResponsibleWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TeacherParticipantBranchWork;
use app\models\work\TeacherParticipantWork;
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

    static private function convertMillimetersToTwips($millimeters)
    {
        return floor($millimeters * 56.7);
        // переход на новую строку в едином тексте "<w:br/>"
    }

    static public function Enrolment ($order_id)
    {
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
        $cell->addText('РЕГИОНАЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'));
        $cell = $table->addCell(2000, array('borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText(' ШКОЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'));
        $cell = $table->addCell(22000, array('valign' => 'bottom', 'borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText('  414000, г. Астрахань, ул. Адмиралтейская, д. 21, помещение № 66', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addImage(Yii::$app->basePath.'/templates/logo.png', array('width'=>100, 'height'=>40, 'align'=>'left'));
        $cell = $table->addCell(2000, array('valign' => 'top'));
        $cell->addText('ТЕХНОПАРК', array('name' => 'Calibri', 'size' => '14'), array('align' => 'center'));
        $cell = $table->addCell(22000);
        $cell->addText(' +7 8512 442428 • schooltech@astrobl.ru • www.школьныйтехнопарк.рф', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array('align' => 'right', 'spaceAfter' => 0));
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

        $countGroup = count($groups);

        $groupsID = [];
        foreach ($groups as $group)
            $groupsID[] = $group->training_group_id;
        $allGroups = $trG->where(['in', 'id', $groupsID])->all();
        $programsID = [];
        foreach ($allGroups as $oneGroup)
            $programsID[] = $oneGroup->training_program_id;
        $countProgram = count($program->where(['in', 'id', $programsID])->all());

        $teachersID = [];
        foreach ($groups as $group)
        {
            $trGs = $teacher->where(['training_group_id' => $group->training_group_id])->all();
            foreach ($trGs as $trGr)
                $teachersID [] = $trGr->teacher_id;
        }
        $countTeacher = count(array_unique($teachersID));

        if ($trG->where(['id' => $groups[0]->training_group_id])->one()->budget === 1)
        {
            $text = 'В соответствии с ч. 1 ст. 53 Федерального закона от 29.12.2012                    № 273-ФЗ «Об образовании в Российской Федерации», Правилами приема обучающихся в государственное автономное образовательное учреждение Астраханской области дополнительного образования «Региональный школьный технопарк» на обучение по дополнительным общеразвивающим программам, на основании заявлений о приеме на обучение по ';
            if ($countProgram == 1)
                $text .= 'дополнительной общеразвивающей программе';
            else
                $text .= 'дополнительным общеразвивающим программам';
        }
        else
            $text = 'В соответствии с ч. 1, ч. 2 ст. 53 Федерального закона от 29.12.2012                    № 273-ФЗ «Об образовании в Российской Федерации», Положением об оказании платных дополнительных образовательных услуг в государственном автономном образовательном учреждении Астраханской области дополнительного образования «Региональный школьный технопарк», на основании договоров об оказании дополнительных платных образовательных услуг и представленных документов';
        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));

        $section->addText('ПРИКАЗЫВАЮ:', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        $text = '          1.	Зачислить обучающихся с «' . date("d", strtotime($order->order_date)) . '» ' . WordWizard::Month(date("m", strtotime($order->order_date))) . ' ' . date("Y", strtotime($order->order_date)) . ' г.';
        if ($countGroup == 1)
            $text .= ' в учебную группу ';
        else
            $text .= ' в учебные группы ';
        $text .= 'ГАОУ АО ДО «РШТ» на обучение по ';
        if ($countProgram == 1)
            $text .= 'дополнительной общеразвивающей программе ';
        else
            $text .= 'дополнительным общеразвивающим программам ';
        $text .= 'согласно Приложению № 1 к настоящему приказу.';
        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        if ($countTeacher == 1)
            $text = '          2.	Назначить руководителем ';
        else
            $text = '          2.	Назначить руководителями ';
        if ($countGroup == 1)
            $text .= 'учебной группы ';
        else
            $text .= 'учебных групп ';
        if ($countTeacher == 1)
            $text .= 'работника ГАОУ АО ДО «РШТ», указанного в Приложении № 1 к настоящему приказу.';
        else
            $text .= 'работников ГАОУ АО ДО «РШТ», указанных в Приложении № 1 к настоящему приказу.';
        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        if ($order->executor_id == $order->bring_id)
            $text = '          3.	Назначить работника, ответственного за организацию образовательного процесса и контроль соблюдения расписания ';
        else
            $text = '          3.	Назначить работников, ответственных за организацию образовательного процесса и контроль соблюдения расписания ';
        if ($countGroup == 1)
            $text .= 'учебной группы согласно Приложению № 2 к настоящему приказу.';
        else
            $text .= 'учебных групп согласно Приложению № 2 к настоящему приказу.';
        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        if ($countTeacher === 1)
            $text = '          4.	Руководителю ';
        else
            $text = '          4.	Руководителям ';
        if ($countGroup == 1)
            $text .= 'учебной группы ';
        else
            $text .= 'учебных групп ';
        $text .= 'проводить с обучающимися инструктажи по технике безопасности в соответствии с ';
        if ($countProgram == 1)
            $text .= 'дополнительной общеразвивающей программой.';
        else
            $text .= 'дополнительными общеразвивающими программами.';
        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        if ($order->executor_id == $order->bring_id)
            $text = '          5.	Назначить работника, ответственного за своевременное ознакомление ';
        else
            $text = '          5.	Назначить работников, ответственных за своевременное ознакомление ';
        if ($countTeacher === 1)
            $text .= 'руководителя ';
        else
            $text .= 'руководителей ';
        if ($countGroup == 1)
            $text .= 'учебной группы ';
        else
            $text .= 'учебных групп ';
        $text .= 'с настоящим приказом согласно Приложению № 2 к настоящему приказу.';

        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('          6.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        $section->addTextBreak(2);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Директор');
        $cell = $table->addCell(12000);
        $cell->addText('В.В. Войков', null, array('align' => 'right'));


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
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Приложение №1', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к приказу ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
                . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
                . date("Y", strtotime($order->order_date)) . ' г. '
                . $text, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(1);

        foreach ($groups as $group)
        {
            $trGroup = $trG->where(['id' => $group->training_group_id])->one();
            $section->addText('Идентификатор учебной группы: ' . $trGroup->number);

            $teacherTrG = $teacher->where(['training_group_id' => $group->training_group_id])->all();
            $text = 'Руководитель учебной группы: ';

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
            $section->addText('Дополнительная общеразвивающая программа: «' . $programTrG->name . '»');
            $section->addText('Направленность: ' . mb_strtolower($programTrG->stringFocus));

            $section->addText('Форма обучения: очная (в случаях, установленных законодательными актами, возможно применение электронного обучения, дистанционных образовательных технологий).');

            $section->addText('Срок освоения (ак.ч.): ' . $programTrG->capacity);

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

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Приложение №2', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к приказу ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        $section->addText('Список работников, ответственных за организацию образовательного процесса и контроль соблюдения расписания учебных групп', array('bold' => true), array('align' => 'center'));
        $section->addTextBreak(1);

        $table = $section->addTable(array('borderColor' => '000000', 'borderSize' => '6'));
        $table->addRow();
        $cell = $table->addCell(1000);
        $cell->addText('№', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $cell->addText('Ф.И.О. ответственного работника', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $cell->addText('Должность', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(9000);
        $cell->addText('Зона ответственности', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(1000);
        $cell->addText('1', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $posOne = $pos->where(['people_id' => $order->executor_id])->one();
        $cell->addText($order->executor->secondname . ' ' . mb_substr($order->executor->firstname, 0, 1) . '. ' . mb_substr($order->executor->patronymic, 0, 1) . '. ', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(3000);
        $cell->addText(mb_strtolower(mb_substr($posOne->position->name, 0, 1)) . mb_substr($posOne->position->name, 1), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(10000);
        $text = '- контроль соблюдения расписания ';
        if ($countGroup == 1)
            $text .= 'учебной группы и соответствия тематики проводимых учебных занятий ';
        else
            $text .= 'учебных групп и соответствия тематики проводимых учебных занятий ';
        if ($countProgram == 1)
            $text .= 'дополнительной общеразвивающей программе';
        else
            $text .= 'дополнительным общеразвивающим программам';
        $cell->addText($text, array('size' => '12'), array('align' => 'both', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(1000);
        $cell->addText('2', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $posOne = $pos->where(['people_id' => $order->bring_id])->one();
        $cell->addText($order->bring->secondname . ' ' . mb_substr($order->bring->firstname, 0, 1) . '. ' . mb_substr($order->bring->patronymic, 0, 1) . '. ', array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $cell->addText(mb_strtolower(mb_substr($posOne->position->name, 0, 1)) . mb_substr($posOne->position->name, 1), array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(9000);
        $text = '- своевременное ознакомление ';
        if ($countTeacher == 1)
            $text .= 'руководителя ';
        else
            $text .= 'руководителей ';
        if ($countGroup == 1)
            $text .= 'учебной группы с настоящим приказом';
        else
            $text .= 'учебных групп с настоящим приказом';
        $cell->addText($text, array('size' => '12'), array('align' => 'both', 'spaceAfter' => 0));


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
        $cell->addText('РЕГИОНАЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'));
        $cell = $table->addCell(2000, array('borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText(' ШКОЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'));
        $cell = $table->addCell(22000, array('valign' => 'bottom', 'borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText('  414000, г. Астрахань, ул. Адмиралтейская, д. 21, помещение № 66', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addImage(Yii::$app->basePath.'/templates/logo.png', array('width'=>100, 'height'=>40, 'align'=>'left'));
        $cell = $table->addCell(2000, array('valign' => 'top'));
        $cell->addText('ТЕХНОПАРК', array('name' => 'Calibri', 'size' => '14'), array('align' => 'center'));
        $cell = $table->addCell(22000);
        $cell->addText(' +7 8512 442428 • schooltech@astrobl.ru • www.школьныйтехнопарк.рф', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array('align' => 'right', 'spaceAfter' => 0));
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

        if ($order->study_type == 0)        // Ф-3
            $text = '          В связи с завершением обучения в ГАОУ АО ДО «РШТ», на основании решения аттестационной комиссии/ протоколов жюри/ судейской коллегии/ итоговой диагностической карты от ' .
                '«' . date("d", strtotime($order->order_date)) . '» ' . WordWizard::Month(date("m", strtotime($order->order_date))) . ' ' . date("Y", strtotime($order->order_date)) . ' г.';
        else if ($order->study_type == 1)    //Ф-4
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

        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('ПРИКАЗЫВАЮ:', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        if ($order->study_type == 0 && $countPasta > 1)
        {
            $section->addText('          1.	Отчислить обучающихся согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            $section->addText('          2.	Выдать обучающимся, указанным в Приложении к настоящему приказу, сертификаты об успешном завершении обучения.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        }
        if ($order->study_type == 0 && $countPasta == 1)
        {
            $section->addText('          1.	Отчислить обучающегося согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            $section->addText('          2.	Выдать обучающемуся, указанному в Приложении к настоящему приказу, сертификат об успешном завершении обучения.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        }
        if ($order->study_type == 1 && $countPasta > 1)
        {
            $section->addText('          1.	Отчислить обучающихся согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            $section->addText('          2.	Выдать обучающимся, не прошедшим итоговую форму контроля и указанным в Приложении к настоящему приказу, справки об обучении в ГАОУ АО ДО «РШТ» установленного учреждением образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        }
        if ($order->study_type == 1 && $countPasta == 1)
        {
            $section->addText('          1.	Отчислить обучающегося согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            $section->addText('          2.	Выдать обучающемуся, не прошедшему итоговую форму контроля и указанному в Приложении к настоящему приказу, справку об обучении в ГАОУ АО ДО «РШТ» установленного учреждением образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        }
        if ($order->study_type == 0 || $order->study_type == 1)
            $section->addText('          3.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        if ($order->study_type == 2)
        {
            if ($trG->where(['id' => $groups[0]->training_group_id])->one()->budget === 1)
            {
                if ($countPasta > 1)
                {
                    $section->addText('          1.	Отчислить обучающихся согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                    $section->addText('          2.	Выдать обучающимся, указанным в Приложении к настоящему приказу, справки об обучении в ГАОУ АО ДО «РШТ» установленного учреждением образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                }
                else
                {
                    $section->addText('          1.	Отчислить обучающегося согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                    $section->addText('          2.	Выдать обучающемуся, указанному в Приложении к настоящему приказу, справку об обучении в ГАОУ АО ДО «РШТ» установленного учреждением образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                }
                $section->addText('          3.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            }
            else
            {
                if ($countPasta > 1)
                {
                    $section->addText('          1.	Расторгнуть договора об оказании платных дополнительных образовательных услуг согласно Приложению № 1.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                    $section->addText('          2.	Отчислить обучающихся согласно Приложению № 2 к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                    $section->addText('          3.	Выдать обучающимся, указанным в Приложении № 2 к настоящему приказу, справки об обучении в ГАОУ АО ДО «РШТ» установленного образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                }
                else
                {
                    $section->addText('          1.	Расторгнуть договор об оказании платных дополнительных образовательных услуг согласно Приложению № 1.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                    $section->addText('          2.	Отчислить обучающегося согласно Приложению № 2 к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                    $section->addText('          3.	Выдать обучающемуся, указанному в Приложении № 2 к настоящему приказу, справку об обучении в ГАОУ АО ДО «РШТ» установленного образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                }
                $section->addText('          4.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            }
        }

        if ($order->study_type == 3)
        {
            if ($countPasta > 1)
            {
                $section->addText('          1.	Расторгнуть договора об оказании платных дополнительных образовательных услуг согласно Приложению № 1.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                $section->addText('          2.	Отчислить обучающихся согласно Приложению № 2 к настоящему приказу. ', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                $section->addText('          3.	Выдать обучающимся, указанным в Приложении № 2 к настоящему приказу, справки об обучении в ГАОУ АО ДО «РШТ» установленного образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            }
            else
            {
                $section->addText('          1.	Расторгнуть договор об оказании платных дополнительных образовательных услуг согласно Приложению № 1.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                $section->addText('          2.	Отчислить обучающегося согласно Приложению № 2 к настоящему приказу. ', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
                $section->addText('          3.	Выдать обучающемуся, указанному в Приложении № 2 к настоящему приказу, справку об обучении в ГАОУ АО ДО «РШТ» установленного образца.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
            }
            $section->addText('          4.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        }

        //$section->addText($text, null, array('align' => 'both'));


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
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $cell->addText('Приложение №1', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $cell->addText('к приказу ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
            $table->addRow();
            $cell = $table->addCell(10000);
            $cell->addText('', null, array('spaceAfter' => 0));
            $cell = $table->addCell(8000);
            $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
            if ($order->order_postfix !== NULL)
                $text .= '/' . $order->order_postfix;
            $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
                . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
                . date("Y", strtotime($order->order_date)) . ' г. '
                . $text, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
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
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        if (($order->study_type == 2 && $trG->where(['id' => $groups[0]->training_group_id])->one()->budget !== 1) || $order->study_type == 3)
            $cell->addText('Приложение №2', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        else
            $cell->addText('Приложение', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к приказу ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);//8000 10000
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        foreach ($groups as $group)
        {
            $trGroup = $trG->where(['id' => $group->training_group_id])->one();
            $section->addText('Идентификатор учебной группы: ' . $trGroup->number);

            $teacherTrG = $teacher->where(['training_group_id' => $group->training_group_id])->all();
            $text = 'Руководитель учебной группы: ';

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
            $section->addText('Дополнительная общеразвивающая программа: «' . $programTrG->name . '»');
            $section->addText('Направленность: ' . mb_strtolower($programTrG->stringFocus));

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
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addText('РЕГИОНАЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'));
        $cell = $table->addCell(2000, array('borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText(' ШКОЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'));
        $cell = $table->addCell(22000, array('valign' => 'bottom', 'borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText('  414000, г. Астрахань, ул. Адмиралтейская, д. 21, помещение № 66', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addImage(Yii::$app->basePath.'/templates/logo.png', array('width'=>100, 'height'=>40, 'align'=>'left'));
        $cell = $table->addCell(2000, array('valign' => 'top'));
        $cell->addText('ТЕХНОПАРК', array('name' => 'Calibri', 'size' => '14'), array('align' => 'center'));
        $cell = $table->addCell(22000);
        $cell->addText(' +7 8512 442428 • schooltech@astrobl.ru • www.школьныйтехнопарк.рф', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array('align' => 'right', 'spaceAfter' => 0));
        //----------
        $section->addTextBreak(1);
        $section->addText('ПРИКАЗ', array('bold' => true), array('align' => 'center'));
        $section->addTextBreak(1);

        //----------------
        $order = DocumentOrderWork::find()->where(['id' => $order_id])->one();
        $groups = OrderGroupWork::find()->where(['document_order_id' => $order->id])->all();

        $tempGID = [];
        foreach ($groups as $g)
            $tempGID[] = $g->training_group_id;
        $tempGID[] = 0;

        $part = ForeignEventParticipantsWork::find();
        $teacher = TeacherGroupWork::find();

        //зачисленные переводом дети
        $gPartIN = TrainingGroupParticipantWork::find()
            ->joinWith(['orderGroupParticipants pasta'])
            ->joinWith(['orderGroupParticipants.orderGroup orderGr'])
            ->joinWith(['orderGroupParticipants.orderGroup.documentOrder order'])
            ->where(['order.id' => $order_id])
            ->andWhere(['pasta.status' => 0])
            ->andWhere(['IS NOT', 'pasta.link_id', null])
            ->andWhere(['NOT IN', 'training_group_participant.training_group_id', $tempGID])
            ->groupBy(['training_group_participant.id'])
            ->all();
        $countPart = count($gPartIN);
        //var_dump($gPartIN->createCommand()->getRawSql());

        $groupsID = [];
        foreach ($gPartIN as $tempPart)
        {
            if (!in_array($tempPart->training_group_id, $groupsID))
                $groupsID[] = $tempPart->training_group_id;
        }

        $programsIN = TrainingProgramWork::find()->joinWith(['trainingGroups trG'])->where(['IN', 'trG.id', $groupsID])->groupBy('training_program.id')->all();
        $programsOUT = TrainingProgramWork::find()->joinWith(['trainingGroups trG'])->joinWith(['trainingGroups.orderGroups orderGr'])->where(['orderGr.document_order_id' => $order_id])->all();

        //var_dump($programsOUT->createCommand()->getRawSql());

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
            $text .= '/' . $order->order_postfix;
        $cell->addText($text, null, array('align' => 'right'));
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText($order->order_name, null, array('align' => 'left'));
        $cell = $table->addCell(6000);
        $cell->addTextBreak(1);

        $section->addTextBreak(1);
        if ($order->study_type == 0)
        {
            $text = 'На основании решения Педагогического совета ГАОУ АО ДО «РШТ» от «____»_________ 20___ г. № ______, в соответствии с п. 2.1.1 Положения о порядке и основаниях перевода, отчисления и восстановления обучающихся государственного автономного образовательного учреждения Астраханской области дополнительного образования «Региональный школьный технопарк»';
        }
        if ($order->study_type == 1 || $order->study_type == 2)
        {
            $text = 'На основании ';
            if ($countPart <= 1)
                $text .= 'заявления родителя (или законного представителя) ';
            else
                $text .= 'заявлений родителей (или законных представителей) ';

            if ($order->study_type == 1)
                $text .= 'и решения Педагогического совета ГАОУ АО ДО «РШТ» от «____»_________ 20___ г. № ______, в соответствии с п. 2.1.2 Положения о порядке и основаниях перевода, отчисления и восстановления обучающихся государственного автономного образовательного учреждения Астраханской области дополнительного образования «Региональный школьный технопарк»';
            else if ($order->study_type == 2)
                $text .= 'от «___»_________ 20___ г., в соответствии с п. 2.1.3 Положения о порядке и основаниях перевода, отчисления и восстановления обучающихся государственного автономного образовательного учреждения Астраханской области дополнительного образования «Региональный школьный технопарк»';
        }

        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));

        $section->addText('ПРИКАЗЫВАЮ:', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0, 'indentation' => array('hanging' => -700)));

        if ($order->study_type == 0)
        {
            $text = '          1.	Перевести ';
            if ($countPart <= 1)
                $text .= 'обучающегося, успешно прошедшего итоговую форму контроля, ';
            else
                $text .= 'обучающихся, успешно прошедших итоговую форму контроля, ';
            $text .= 'на следующий год обучения по дополнительным общеразвивающим программам согласно Приложению к настоящему приказу.';
        }
        if ($order->study_type == 1 || $order->study_type == 2)
        {
            // если внезапно, по какой-то причине вошли в условие, значит регистратор приказа накосячил
            if (((count($programsIN) > 1 || count($programsOUT) > 1) && $order->study_type == 1) || ((count($groups) > 1 /*|| count($groupsID) > 1*/) && $order->study_type == 2))
            {
                if ($order->study_type == 1)
                    $message = ['Невозможно сгенерировать приказ, т.к. отсутствуют утвержденные формы! К приказу о переводе из одной ДОП в другую ДОП добавлено слишком много учебных групп с разными образовательными программами.', 'При генерации приказа ID='.$order_id.' обнаружена ошибка: у всех групп (из которой переводят) должна быть одна ДОП, у всех групп в которую переводят тоже должна быть одна ДОП. Регистратор приказа создает приказ по которому отсутствует утвержденная форма'];
                else
                    $message = ['Невозможно сгенерировать приказ, т.к. отсутствуют утвержденные формы! К приказу о переводе из одной группы в другую добавлено слишком много учебных групп.', 'При генерации приказа ID='.$order_id.' обнаружена ошибка: должна быть одна группа из которой переводят и одна группа в которую переводят. Регистратор приказа создает приказ по которому отсутствует утвержденная форма'];
                Yii::$app->session->setFlash('danger', $message[0]);
                Logger::WriteLog(Yii::$app->user->identity->getId(), $message[1]);
                return;
            }

            if ($order->study_type == 1)
            {
                $text = '          1.	Перевести с обучения по дополнительной общеразвивающей программе «' . $programsOUT[0]->name . '» ('. mb_substr(mb_strtolower($programsOUT[0]->stringFocus), 0, mb_strlen($programsOUT[0]->stringFocus) - 2, "utf-8")
                    . 'ой направленности) на обучение по дополнительной общеразвивающей программе «' . $programsIN[0]->name . '» ('. mb_substr(mb_strtolower($programsIN[0]->stringFocus), 0, mb_strlen($programsIN[0]->stringFocus) - 2, "utf-8") . 'ой направленности) ';
            }
            else if ($order->study_type == 2)
            {
                $oldGr = TrainingGroupWork::find()->where(['id' => $groups[0]])->one();
                $newGr = TrainingGroupWork::find()->where(['id' => $groupsID[0]])->one();

                $text = '          1.	Перевести из учебной группы ' . $oldGr->number . ' в учебную группу ' . $newGr->number .  ' в рамках обучения по дополнительной общеразвивающей программе «' . $programsIN[0]->name . '», '
                    . mb_substr(mb_strtolower($programsIN[0]->stringFocus), 0, mb_strlen($programsIN[0]->stringFocus) - 2, "utf-8") . 'ой направленности ';
            }

            if ($countPart <= 1)
                $text .= 'обучающегося согласно Приложению к настоящему приказу.';
            else
                $text .= 'обучающихся согласно Приложению к настоящему приказу.';
        }

        $section->addText($text, array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('          2.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));


        $section->addTextBreak(2);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Директор');
        $cell = $table->addCell(12000);
        $cell->addText('В.В. Войков', null, array('align' => 'right'));


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
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Приложение №1', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к приказу ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(1);


        foreach ($groupsID as $group)
        {
            if ($groups[0] !== $group) {
                $trGroup = TrainingGroupWork::find()->where(['id' => $group])->one();
                $section->addText('Идентификатор учебной группы: ' . $trGroup->number);

                $teacherTrG = $teacher->where(['training_group_id' => $group])->all();
                $text = 'Руководитель учебной группы: ';

                foreach ($teacherTrG as $trg) {
                    $post = [];
                    $pPosB = $pos->where(['people_id' => $trg->teacher_id])->all();
                    foreach ($pPosB as $posOne) {
                        $post [] = $posOne->position_id;
                    }
                    $post = array_unique($post);    // выкинули все повторы
                    $post = array_intersect($post, [15, 16, 35, 44]);   // оставили только преподские должности

                    if (count($post) > 0) {
                        $posName = $positionName->where(['id' => $post[0]])->one();
                        $text .= mb_strtolower($posName->name) . ' ' . $trg->teacherWork->shortName . ', ';
                    } else
                        $text .= $trg->teacherWork->shortName . ', ';
                }
                $text = mb_substr($text, 0, -2);
                $section->addText($text);

                $programTrG = TrainingProgramWork::find()->where(['id' => $trGroup->training_program_id])->one();
                $section->addText('Дополнительная общеразвивающая программа: «' . $programTrG->name . '»');
                $section->addText('Направленность: ' . mb_strtolower($programTrG->stringFocus));

                $section->addText('Форма обучения: очная (в случаях, установленных законодательными актами, возможно применение электронного обучения, дистанционных образовательных технологий).');

                $section->addText('Срок освоения (ак.ч.): ' . $programTrG->capacity);

                $section->addText('Обучающиеся: ');
                $participants = TrainingGroupParticipantWork::find()
                    ->joinWith(['orderGroupParticipants pasta'])
                    ->joinWith(['orderGroupParticipants.orderGroup orderGr'])
                    ->joinWith(['orderGroupParticipants.orderGroup.documentOrder order'])
                    ->where(['order.id' => $order_id])
                    ->andWhere(['pasta.status' => 0])
                    ->andWhere(['IS NOT', 'pasta.link_id', null])
                    ->andWhere(['training_group_participant.training_group_id' => $group])
                    ->groupBy(['training_group_participant.id'])
                    ->all();
                for ($i = 0; $i < count($participants); $i++) {
                    $participant = ForeignEventParticipantsWork::find()->where(['id' => $participants[$i]->participant_id])->one();
                    $section->addText($i + 1 . '. ' . $participant->getFullName());
                }
                $section->addTextBreak(2);
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

    static public function ProtocolCommission ($order_id)
    {
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
        $cell->addText('РЕГИОНАЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'));
        $cell = $table->addCell(2000, array('borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText(' ШКОЛЬНЫЙ', array('name' => 'Calibri', 'size' => '14'));
        $cell = $table->addCell(22000, array('valign' => 'bottom', 'borderSize' => 2, 'borderColor' => 'white', 'borderBottomColor' => 'red'));
        $cell->addText('  414000, г. Астрахань, ул. Адмиралтейская, д. 21, помещение № 66', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array( 'align' => 'right'));
        $table->addRow();
        $cell = $table->addCell(2000);
        $cell->addImage(Yii::$app->basePath.'/templates/logo.png', array('width'=>100, 'height'=>40, 'align'=>'left'));
        $cell = $table->addCell(2000, array('valign' => 'top'));
        $cell->addText('ТЕХНОПАРК', array('name' => 'Calibri', 'size' => '14'), array('align' => 'center'));
        $cell = $table->addCell(22000);
        $cell->addText(' +7 8512 442428 • schooltech@astrobl.ru • www.школьныйтехнопарк.рф', array('name' => 'Calibri', 'size' => '9', 'color' => 'red'), array('align' => 'right', 'spaceAfter' => 0));
        //----------
        $section->addTextBreak(1);
        $section->addText('ПРОТОКОЛ', array('bold' => true), array('align' => 'center'));
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
        $pos = PeoplePositionBranchWork::find();
        $positionName = PositionWork::find();

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('«' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г.');
        $cell = $table->addCell(12000);
        $cell->addText('№ #', null, array('align' => 'right'));
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText('заседание аттестационной комиссии', null, array('align' => 'left'));
        $cell->addText('Регионального школьного технопарка', null, array('align' => 'left'));
        $cell = $table->addCell(6000);
        $cell->addTextBreak(1);
        $section->addTextBreak(1);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText('Председатель – Войков Владимир Владимирович', null, array('align' => 'left'));
        $cell->addText('Секретарь – ### ### ###', null, array('align' => 'left'));
        $cell->addText('Присутствовали: # человек', null, array('align' => 'left'));
        $cell = $table->addCell(6000);
        $cell->addTextBreak(1);

        $section->addTextBreak(1);

        $section->addText('В заседании участвуют:', null, array('align' => 'both'));
        $section->addText('          1.	Председатель комиссии Войков В.В.', null, array('align' => 'both'));
        $section->addText('          2.	Заместитель председателя комиссии Воеводин И.Г.', null, array('align' => 'both'));
        $section->addText('          3.	Члены комиссии:', null, array('align' => 'both'));
        $section->addText('                3.1.	### ### ###', null, array('align' => 'both'));
        $section->addText('                3.2.	### ### ###', null, array('align' => 'both'));
        $section->addText('          4.	Секретарь – ### ### ###', null, array('align' => 'both'));

        $section->addTextBreak(2);
        $section->addText('ПОВЕСТКА ДНЯ', null, array('align' => 'center'));
        $section->addTextBreak(1);
        $section->addText('          1.	Принятие решения о результатах проверки аттестационных работ', null, array('align' => 'both'));


        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));


        $section->addText('1. СЛУШАЛИ:', array('lineHeight' => 1.5), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('Войков Владимир Владимирович ознакомил присутствующих со списком проектантов успешно выполнивших проекты и рекомендованных к отчислению (приложение №1).', array('lineHeight' => 1.5), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('Войков Владимир Владимирович задал вопрос каждому из присутствующих членов комиссии, ознакомился ли он (она) со всеми аттестационными работами (а именно исследовательскими, техническими и творческими проектами).', array('lineHeight' => 1.5), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('Все присутствующие ответили на этот вопрос утвердительно.', array('lineHeight' => 1.5), array('align' => 'both', 'spaceAfter' => 0));
        $section->addTextBreak(1);
        $section->addText('Войков Владимир Владимирович задал вопрос каждому из присутствующих, считает ли он (она), что среди аттестационных работ есть работы, которые не могут быть признаны выполненными удовлетворительно для получения положительного решения об успешном выполнении проекта.', array('lineHeight' => 1.5), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('Все присутствующие ответили на этот вопрос отрицательно.', array('lineHeight' => 1.5), array('align' => 'both', 'spaceAfter' => 0));
        $section->addTextBreak(1);
        $section->addText('ПОСТАНОВИЛИ:', array('lineHeight' => 1.5), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('          1.1.	Признать проектантов (приложение №1) успешно справившимися с выполнением исследовательских, технических, творческих проектов и выдать сертификаты об успешном выполнении проекта.', array('lineHeight' => 1.5), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('          1.2.	Отчислить проектантов (приложение №1) в связи с окончанием проектной деятельности.', array('lineHeight' => 1.5), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('Голосовали: присутствовало # членов комиссии. # голосов – «за», 0 голосов – «против»', array('lineHeight' => 1.5), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('Приложение: списки групп.', array('lineHeight' => 1.5), array('align' => 'both', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText('Председатель аттестационной комиссии');
        $cell = $table->addCell(6000);
        $cell->addText('___________ В.В. Войков', null, array('align' => 'left'));
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText('Секретарь аттестационной комиссии');
        $cell = $table->addCell(6000);
        $cell->addText('___________ # # ###', null, array('align' => 'left'));

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(6000);
        $cell->addText('Приложение № 1', array('size' => '14'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(6000);
        $cell->addText('к протоколу заседания', array('size' => '14'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(12000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(6000);
        $cell->addText('аттестационной комисии', array('size' => '14'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(12000);//8000 10000
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(6000);
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . '№ #', array('size' => '14'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        foreach ($groups as $group)
        {
            $trGroup = $trG->where(['id' => $group->training_group_id])->one();
            $section->addText('Идентификатор учебной группы: ' . $trGroup->number);

            $teacherTrG = $teacher->where(['training_group_id' => $group->training_group_id])->all();
            $text = 'Руководитель учебной группы: ';

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
            $section->addText('Дополнительная общеразвивающая программа: «' . $programTrG->name . '»');
            $section->addText('Направленность: ' . mb_strtolower($programTrG->stringFocus));

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

        $text = 'Протокол АК к приказу ' . date("Ymd", strtotime($order->order_date)) . '_' . $order->order_number . $order->order_copy_id . $order->order_postfix . '_' . mb_substr($order->order_name, 0, 20);
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

    static public function ParticipationEvent ($order_id)
    {
        ini_set('memory_limit', '512M');

        $inputData = new PhpWord();
        $inputData->setDefaultFontName('Times New Roman');
        $inputData->setDefaultFontSize(14);

        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(30),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));

        $section->addText('Министерство образования и науки Астраханской области', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('государственное автономное образовательное учреждение', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('Астраханской области дополнительного образования', array('lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('«Региональный школьный технопарк»', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('ГАОУ АО ДО «РШТ»', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('ПРИКАЗ', array('bold' => true, 'lineHeight' => 1.0), array('align' => 'center', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        /*----------------*/
        $order = DocumentOrderWork::find()->where(['id' => $order_id])->one();
        $res = ResponsibleWork::find()->where(['document_order_id' => $order->id])->all();
        $supplement = DocumentOrderSupplementWork::find()->where(['document_order_id' => $order_id])->one();
        $foreignEvent = ForeignEventWork::find()->where(['order_participation_id' => $order_id])->one();
        $teacherParts = TeacherParticipantWork::find()->where(['foreign_event_id' => $foreignEvent->id])->all();

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

        $section->addText($order->order_name, null, array('align' => 'both'));
        $section->addTextBreak(1);

        /* переменная цели и соответствия*/
        $purpose = $supplement->foreignEventGoalsWork->name;
        $invitations = ['', ' и в соответствии с Регламентом', ' и в соответствии с Письмом', ' и в соответствии с Положением'];
        $invitation = $invitations[$supplement->compliance_document].' '.$supplement->document_details;
        $section->addText('С целью '.$purpose.$invitation, null, array('align' => 'both', 'indentation' => array('hanging' => -700)));
        $section->addTextBreak(1);

        $section->addText('ПРИКАЗЫВАЮ:', array('lineHeight' => 1.0), array('spaceAfter' => 0));
        $section->addText('1.	Принять участие в мероприятии «'.$foreignEvent->name.'» (далее – мероприятие) и утвердить перечень учащихся, участвующих в мероприятии, и педагогов, ответственных за подготовку и контроль результатов участия в мероприятии, согласно Приложению к настоящему приказу.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('2.	Назначить ответственным за сбор и предоставление информации об участии в мероприятии для внесения в Цифровую систему хранения документов ГАОУ АО ДО «РШТ» (далее – ЦСХД) '.$supplement->collectorWork->positionAndShortFullName.'.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('3.	Определить срок предоставления информации об участии в мероприятии: '.$supplement->information_deadline.' рабочих дней со дня завершения мероприятия.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('4.	Назначить ответственным за внесение информации об участии в мероприятии в ЦСХД '.$supplement->contributorWork->positionAndShortFullName.'.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('5.	Определить срок для внесения информации об участии в мероприятии: '.$supplement->input_deadline.' рабочих дней со дня завершения мероприятия.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('6.	Назначить ответственным за методический контроль подготовки учащихся к участию в мероприятии и информационное взаимодействие с организаторами мероприятия '.$supplement->methodologistWork->positionAndShortFullName.'.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('7.	Назначить ответственным за информирование работников о настоящем приказе '.$supplement->informantWork->positionAndShortFullName.'.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));
        $section->addText('8.	Контроль исполнения приказа оставляю за собой.', array('lineHeight' => 1.0), array('align' => 'both', 'spaceAfter' => 0));

        $section->addTextBreak(2);

        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(6000);
        $cell->addText('Директор');
        $cell = $table->addCell(12000);
        $cell->addText('В.В. Войков', null, array('align' => 'right'));

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

        /*тут перечень учащихся*/
        $section = $inputData->addSection(array('marginTop' => WordWizard::convertMillimetersToTwips(20),
            'marginLeft' => WordWizard::convertMillimetersToTwips(20),
            'marginBottom' => WordWizard::convertMillimetersToTwips(20),
            'marginRight' => WordWizard::convertMillimetersToTwips(15) ));
        $table = $section->addTable();
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Приложение', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('к приказу директора', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $text = '№ ' . $order->order_number . '/' . $order->order_copy_id;
        if ($order->order_postfix !== NULL)
            $text .= '/' .  $order->order_postfix;
        $cell->addText('от «' . date("d", strtotime($order->order_date)) . '» '
            . WordWizard::Month(date("m", strtotime($order->order_date))) . ' '
            . date("Y", strtotime($order->order_date)) . ' г. '
            . $text, array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $cell->addTextBreak(1);
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('УТВЕРЖДАЮ', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('Директор ГАОУ АО ДО «РШТ»', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $table->addRow();
        $cell = $table->addCell(10000);
        $cell->addText('', null, array('spaceAfter' => 0));
        $cell = $table->addCell(8000);
        $cell->addText('_________________ В.В. Войков', array('size' => '12'), array('align' => 'left', 'spaceAfter' => 0));
        $section->addTextBreak(2);

        $section->addText('Перечень учащихся ГАОУ АО ДО «РШТ» – участников мероприятии', array('bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('«'.$foreignEvent->name.'» –', array('bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('с указанием педагогов, ответственных за подготовку участников и контроль', array('bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $section->addText('результатов участия', array('bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $section->addTextBreak(1);

        $table = $section->addTable(array('borderColor' => '000000', 'borderSize' => '6'));
        $table->addRow();
        $cell = $table->addCell(1000);
        $cell->addText('<w:br/><w:br/><w:br/>№ п/п', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $cell->addText('<w:br/><w:br/><w:br/>Ф.И.О. участника', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(3000);
        $cell->addText('Номинация (разряд, трек, класс, и т.п.), в которой производится участие в мероприятии', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(3000);
        $cell->addText('Направленность образовательных программ, к которой относится участие в мероприятии', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(3000);
        $cell->addText('Отдел ГАОУ АО ДО «РШТ», на базе которого проведена подготовка участника', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));
        $cell = $table->addCell(4000);
        $cell->addText('Ф.И.О. педагога, ответственного за подготовку участника и контроль результатов его участия', array('size' => '12', 'bold' => true), array('align' => 'center', 'spaceAfter' => 0));

        $tBranchs = TeacherParticipantBranchWork::find();
        foreach ($teacherParts as $key => $oneActPart)
        {
            $table->addRow();
            $cell = $table->addCell(1000);
            $cell->addText($key+1, array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
            $cell = $table->addCell(4000);
            $cell->addText($oneActPart->participantWork->fullName, array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
            $cell = $table->addCell(3000);
            $cell->addText($oneActPart->nomination, array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
            $cell = $table->addCell(3000);
            $cell->addText($oneActPart->focus0->name, array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));

            $cell = $table->addCell(3000);
            $branchs = $tBranchs->where(['teacher_participant_id' => $oneActPart->id])->all();
            foreach ($branchs as $branch)
                $cell->addText($branch->branchWork->name, array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));

            $cell = $table->addCell(4000);
            $cell->addText(mb_substr($oneActPart->teacherWork->firstname, 0, 1) .'. '. mb_substr($oneActPart->teacherWork->patronymic, 0, 1) .'. '. $oneActPart->teacherWork->secondname, array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
            if ($oneActPart->teacher2_id != null)
                $cell->addText(mb_substr($oneActPart->teacher2Work->firstname, 0, 1) .'. '. mb_substr($oneActPart->teacher2Work->patronymic, 0, 1) .'. '. $oneActPart->teacher2Work->secondname, array('size' => '12'), array('align' => 'center', 'spaceAfter' => 0));
        }


        $text = 'Пр.' . date("Ymd", strtotime($order->order_date)) . '_' . $order->order_number . $order->order_copy_id . $order->order_postfix . '_' . substr($order->order_name, 0, 35);
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