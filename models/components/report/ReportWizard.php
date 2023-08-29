<?php

namespace app\models\components\report;

//--Класс для создания отчетов в формате MS Excel--
//----Бизнес-логика реализована в методах класса SupportReportFunctions
use Yii;

class ReportWizard
{
    //--Функция генерации гос. задания--
    public function GenerateGZ()
    {
        ini_set('max_execution_time', '6000');
        ini_set('memory_limit', '2048M');
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/report_GZ.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/report_GZ.xlsx');


    }
}