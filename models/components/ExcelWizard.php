<?php


namespace app\models\components;


use app\models\common\ForeignEventParticipants;
use app\models\common\RussianNames;
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

    static public function GetAllParticipants($filename)
    {

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
        while ($startRow < 100 && strlen($tempValue) < 2)
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

        $participants = [new ForeignEventParticipants];
        for ($i = 0; $i != count($names); $i++)
        {
            $fio = explode(" ", $names[$i]);
            if (count($fio) == 3)
                $newParticipant = ForeignEventParticipants::find()->where(['firstname' => $fio[1]])->andWhere(['secondname' => $fio[0]])->andWhere(['patronymic' => $fio[2]])->andWhere(['birthdate' => date("Y-m-d", strtotime($birthdates[$i]))])->one();
            else
                $newParticipant = ForeignEventParticipants::find()->where(['firstname' => $fio[1]])->andWhere(['secondname' => $fio[0]])->andWhere(['birthdate' => strtotime(date("Y-m-d", strtotime($birthdates[$i])))])->one();
            if ($newParticipant == null)
            {
                $newParticipant = new ForeignEventParticipants();
                $newParticipant->firstname = $fio[1];
                $newParticipant->secondname = $fio[0];
                if (count($fio) == 3)
                    $newParticipant->patronymic = $fio[2];
                $newParticipant->birthdate = date("Y-m-d", strtotime($birthdates[$i]));
                $newParticipant->sex = self::GetSex($fio[1]);
                $newParticipant->save();
            }
        }
    }

}