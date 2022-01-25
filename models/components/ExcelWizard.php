<?php


namespace app\models\components;


use app\models\common\ForeignEventParticipants;
use app\models\common\RussianNames;
use app\models\work\LessonThemeWork;
use app\models\work\ThematicPlanWork;
use app\models\work\TrainingGroupParticipantWork;
use Yii;

class ExcelWizard
{
    static public function GetSex($name)
    {
        $searchName = RussianNames::find()->where(['name' => $name])->one();
        if ($searchName == null)
            return "Другое";
        if ($searchName->Sex == "М") return "Мужской";
        else return "Женский";
    }

    static public function WriteUtp($filename, $training_program_id)
    {
        ini_set('memory_limit', '512M');
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/upload/files/program/temp/'.$filename);
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/upload/files/program/temp/'.$filename);
        $index = 2;
        while ($index <= $inputData->getActiveSheet()->getHighestRow() && strlen($inputData->getActiveSheet()->getCellByColumnAndRow(0, $index)->getValue()) > 1)
        {
            $theme = $inputData->getActiveSheet()->getCellByColumnAndRow(0, $index)->getValue();
            $controlId = $inputData->getActiveSheet()->getCellByColumnAndRow(1, $index)->getValue();
            $tp = new ThematicPlanWork();
            $tp->theme = $theme;
            $tp->control_type_id = $controlId;
            $tp->training_program_id = $training_program_id;
            $tp->save();
            $index++;
        }
        unlink(Yii::$app->basePath.'/upload/files/program/temp/'.$filename);
    }

    static public function DownloadKUG($training_group_id)
    {
        ini_set('memory_limit', '512M');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="simple.xlsx"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/template_KUG.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/template_KUG.xlsx');


        $lessons = LessonThemeWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['trainingGroupLesson.training_group_id' => $training_group_id])->all();
        $c = 1;

        foreach ($lessons as $lesson)
        {

            $inputData->getActiveSheet()->setCellValueByColumnAndRow(0, 12 + $c, $c);

            $inputData->getActiveSheet()->setCellValueByColumnAndRow(1, 12 + $c, $lesson->trainingGroupLesson->lesson_date);

            $inputData->getActiveSheet()->setCellValueByColumnAndRow(2, 12 + $c, mb_substr($lesson->trainingGroupLesson->lesson_start_time, 0, -3).' - '.mb_substr($lesson->trainingGroupLesson->lesson_end_time, 0, -3));

            $inputData->getActiveSheet()->setCellValueByColumnAndRow(3, 12 + $c, $lesson->theme);
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(4, 12 + $c, $lesson->trainingGroupLesson->duration);
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(5, 12 + $c, "Групповая");
            $inputData->getActiveSheet()->setCellValueByColumnAndRow(6, 12 + $c, $lesson->controlType->name);
            $c++;
        }

        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel2007');
        $writer->save('php://output');
    }

    static public function WriteAllCertNumbers($filename, $training_group_id)
    {
        ini_set('memory_limit', '512M');
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/upload/files/bitrix/groups/'.$filename);
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/upload/files/bitrix/groups/'.$filename);
        $index = 2;
        while ($index < $inputData->getActiveSheet()->getHighestRow() && strlen($inputData->getActiveSheet()->getCellByColumnAndRow(2, $index)->getValue()) > 5)
        {
            $fio = $inputData->getActiveSheet()->getCellByColumnAndRow(2, $index)->getValue();
            $fio = explode(" ", $fio);
            if (count($fio) > 1)
            {
                $people = null;
                if (count($fio) == 2)
                {
                    $people = TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['training_group_id' => $training_group_id])
                        ->andWhere(['participant.secondname' => $fio[0]])->andWhere(['participant.firstname' => $fio[1]])->one();

                }
                if (count($fio) == 3)
                {
                    $people = TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['training_group_id' => $training_group_id])
                        ->andWhere(['participant.secondname' => $fio[0]])->andWhere(['participant.firstname' => $fio[1]])->andWhere(['participant.patronymic' => $fio[2]])->one();
                }
                if ($people !== null)
                {
                    $people->certificat_number = strval($inputData->getActiveSheet()->getCellByColumnAndRow(3, $index)->getValue());
                    $people->save();
                }
                $index++;
            }
        }
    }

    static public function GetAllParticipants($filename)
    {
        ini_set('memory_limit', '512M');
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/upload/files/bitrix/groups/'.$filename);
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/upload/files/bitrix/groups/'.$filename);
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel2007');
        $splitName = explode(".", $filename);
        $newFilename = $splitName[0].'_new'.'.xls';//.$splitName[1];
        $inputData = $writer->save(Yii::$app->basePath.'/upload/files/bitrix/groups/'.$newFilename);
        $newReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $inputData = $newReader->load(Yii::$app->basePath.'/upload/files/bitrix/groups/'.$newFilename);

        $startRow = 1;

        $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow(0, $startRow)->getValue();
        while ($startRow < 100 && strlen($tempValue) < 4)
        {
            $startRow++;
            $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow(0, $startRow)->getValue();
        }
        $fioColumnIndex = 0;
        $tempValue = '_';
        $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow($fioColumnIndex, $startRow)->getValue();

        while ($fioColumnIndex < 100 && $tempValue !== 'Фамилия Имя Отчество проектанта')
        {
            $fioColumnIndex++;
            $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow($fioColumnIndex, $startRow)->getValue();
        }

        $birthdateColumnIndex = 0;
        $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow($birthdateColumnIndex, $startRow)->getValue();
        while ($birthdateColumnIndex < 100 && $tempValue !== 'Дата рождения (л)')
        {
            $birthdateColumnIndex++;
            $tempValue = $inputData->getActiveSheet()->getCellByColumnAndRow($birthdateColumnIndex, $startRow)->getValue();
        }
        $names = [];
        $curName = "_";
        $startIndex = $startRow + 1;
        $mainIndex = 0;

        while ($mainIndex < $inputData->getActiveSheet()->getHighestRow() - $startRow)
        {
            $curName = $inputData->getActiveSheet()->getCellByColumnAndRow($fioColumnIndex, $startIndex + $mainIndex)->getValue();
            if ($curName !== null)
                $names[] = $curName;
            else
                $names[] = "none none none";
            $mainIndex++;
        }

        $birthdates = [];
        $curDate = "_";
        $startIndex = $startRow + 1;
        $mainIndex = 0;
        while ($mainIndex < $inputData->getActiveSheet()->getHighestRow() - $startRow)
        {
            $curDate = $inputData->getActiveSheet()->getCellByColumnAndRow($birthdateColumnIndex, $startIndex + $mainIndex)->getFormattedValue();
            $birthdates[] = $curDate;
            $mainIndex++;
        }
        //unset($birthdates[count($birthdates) - 1]);
        //unset($names[count($names) - 1]);

        $participants = array();
        for ($i = 0; $i != count($names); $i++)
        {
            $fio = explode(" ", $names[$i]);
            if (count($fio) == 3)
                $newParticipant = ForeignEventParticipants::find()->where(['firstname' => $fio[1]])->andWhere(['secondname' => $fio[0]])->andWhere(['patronymic' => $fio[2]])->andWhere(['birthdate' => date("Y-m-d", strtotime($birthdates[$i]))])->one();
            else {
                if (count($fio) > 3)
                {
                    $patr = '';
                    for ($j = 2; $j != count($fio); $j++)
                        $patr .= $fio[$j].' ';
                    $patr = mb_substr($patr, 0, -1);
                    $newParticipant = ForeignEventParticipants::find()->where(['firstname' => $fio[1]])->andWhere(['secondname' => $fio[0]])->andWhere(['patronymic' => $patr])->andWhere(['birthdate' => date("Y-m-d", strtotime($birthdates[$i]))])->one();
                }
                else
                    $newParticipant = ForeignEventParticipants::find()->where(['firstname' => $fio[1]])->andWhere(['secondname' => $fio[0]])->andWhere(['birthdate' => date("Y-m-d", strtotime($birthdates[$i]))])->one();
            }
            if ($newParticipant == null)
            {
                $newParticipant = new ForeignEventParticipants();
                $newParticipant->firstname = $fio[1];
                $newParticipant->secondname = $fio[0];
                if (count($fio) == 3)
                    $newParticipant->patronymic = $fio[2];
                if (count($fio) > 3)
                {
                    $patr = '';
                    for ($j = 2; $j != count($fio); $j++)
                        $patr .= $fio[$j].' ';
                    $patr = mb_substr($patr, 0, -1);
                    $newParticipant->patronymic = $patr;
                }
                $newParticipant->birthdate = date("Y-m-d", strtotime($birthdates[$i]));
                $newParticipant->sex = self::GetSex($fio[1]);
                $newParticipant->save();
            }
            $participants[] = $newParticipant;
        }
        return $participants;
    }

}