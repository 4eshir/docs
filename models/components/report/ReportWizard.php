<?php

namespace app\models\components\report;

//--Класс для создания отчетов в формате MS Excel--
//----Бизнес-логика реализована в методах класса SupportReportFunctions
use app\models\components\ExcelWizard;
use app\models\work\AllowRemoteWork;
use app\models\work\BranchWork;
use app\models\work\FocusWork;
use Yii;

class ReportWizard
{
    //--Функция генерации гос. задания--
    public function GenerateGZ($start_date, $end_date)
    {
        //--Задаем параметры для выполнения скриптов и открываем файл шаблона--
        ini_set('max_execution_time', '6000');
        ini_set('memory_limit', '2048M');
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/report_GZ.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/report_GZ.xlsx');
        //---------------------------------------------------------------------


        //получаем количество детей, подавших более 1 заявления и считаем процент защитивших проект / призеров победителей мероприятий

        //Отдел Технопарк (тех. направленность)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
                                                    [BranchWork::TECHNO], [FocusWork::TECHNICAL], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 16,
            count(SupportReportFunctions::GetDoubleParticipantsFromGroup(ReportConst::PROD, $targetGroups, ReportConst::AGES_ALL, $end_date)) /
            count(SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date)));

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 16,
            count(SupportReportFunctions::GetDoubleParticipantsFromGroup(ReportConst::PROD, $targetGroups, ReportConst::AGES_ALL, $end_date)) /
            count(SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date)));


        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 18, ExcelWizard::GetPercentProjectParticipant($start_date, $end_date, 2, 1));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 19, ExcelWizard::GetPercentEventParticipants($start_date, $end_date, 2, 1, 1));
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 16)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 16)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 18)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 18)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 19)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 19)->getStyle()->getAlignment()->setHorizontal('center');

        //-------------------------------------


        //--Формирование заголовков и ответа сервера--
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="report.xlsx"');
        header('Cache-Control: max-age=0');
        mb_internal_encoding('Windows-1251');
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel2007');
        $writer->save('php://output');
        exit;
        //--------------------------------------------
    }
}