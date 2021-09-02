<?php


namespace app\models\components;


use app\models\common\ForeignEventParticipants;
use app\models\common\RussianNames;
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
                else
                {
                    var_dump($fio);
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