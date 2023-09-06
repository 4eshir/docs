<?php

namespace app\models\components\report;

//--Класс для создания отчетов в формате MS Excel--
//----Бизнес-логика реализована в методах класса SupportReportFunctions
use app\models\components\ExcelWizard;
use app\models\work\AllowRemoteWork;
use app\models\work\BranchWork;
use app\models\work\EventLevelWork;
use app\models\work\FocusWork;
use app\models\work\TeamWork;
use app\models\work\VisitWork;
use Yii;
use yii\db\Query;

class ReportWizard
{
    //--Функция генерации гос. задания--
    static public function GenerateGZ($start_date, $end_date, $visit_type = VisitWork::PRESENCE_AND_ABSENCE)
    {
        //--Задаем параметры для выполнения скриптов и открываем файл шаблона--
        ini_set('max_execution_time', '6000');
        ini_set('memory_limit', '2048M');
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/report_GZ.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/report_GZ.xlsx');
        //---------------------------------------------------------------------


        /*
         * |------------------------------------------|
         * | Подсчет основных показателей гос.задания |
         * |------------------------------------------|
         */

        //Получаем количество детей, подавших более 1 заявления и считаем процент защитивших проект / призеров победителей мероприятий

        //Отдел Технопарк (тех. направленность)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
                                                    [BranchWork::TECHNO], [FocusWork::TECHNICAL], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        // Процент обучающихся в 2+ группах
        $target = count(SupportReportFunctions::GetDoubleParticipantsFromGroup(ReportConst::PROD, $targetGroups, ReportConst::AGES_ALL, $end_date));
        $allTechoparkTechnical = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $all = count($allTechoparkTechnical);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 16, $all == 0 ? 0 : ($target / $all) * 100);


        // Процент успешно защитивших проект (получивших сертификат)
        $target = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $allTechoparkTechnical));
        //$all = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date));

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 18, $all == 0 ? 0 : ($target * 1.0 / $all) * 100);


        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 1, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::TECHNO], [FocusWork::TECHNICAL]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 19, count($all[0]) == 0 ? 0 : (count($target) * 1.0 / count($all[0])) * 100);

        // Стилизация ячеек
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 16)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 16)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 18)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 18)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 19)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 19)->getStyle()->getAlignment()->setHorizontal('center');

        //-------------------------------------


        //Отдел ЦДНТТ (тех. направленность)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::CDNTT], [FocusWork::TECHNICAL], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        // Процент обучающихся в 2+ группах
        $target = count(SupportReportFunctions::GetDoubleParticipantsFromGroup(ReportConst::PROD, $targetGroups, ReportConst::AGES_ALL, $end_date));
        $allCdnttTechnical = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $all = count($allCdnttTechnical);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 21, $all == 0 ? 0 : ($target * 1.0 / $all) * 100);

        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 1, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::CDNTT], [FocusWork::TECHNICAL]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 23, count($all[0]) == 0 ? 0 : (count($target) * 1.0 / count($all[0])) * 100);

        // Стилизация ячеек
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 21)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 21)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 23)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 23)->getStyle()->getAlignment()->setHorizontal('center');

        //---------------------------------


        //Отдел ЦДНТТ (худ. направленность)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::CDNTT], [FocusWork::ART], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        // Процент обучающихся в 2+ группах
        $target = count(SupportReportFunctions::GetDoubleParticipantsFromGroup(ReportConst::PROD, $targetGroups, ReportConst::AGES_ALL, $end_date));
        $allCdnttArt = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $all = count($allCdnttArt);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 25, $all == 0 ? 0 : ($target * 1.0 / $all) * 100);

        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 1, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::CDNTT], [FocusWork::ART]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 27, count($all[0]) == 0 ? 0 : (count($target) * 1.0 / count($all[0])) * 100);

        // Стилизация ячеек
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 25)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 25)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 27)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 27)->getStyle()->getAlignment()->setHorizontal('center');

        //---------------------------------


        //Отдел ЦДНТТ (соц-пед. направленность)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::CDNTT], [FocusWork::SOCIAL], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        // Процент обучающихся в 2+ группах
        $target = count(SupportReportFunctions::GetDoubleParticipantsFromGroup(ReportConst::PROD, $targetGroups, ReportConst::AGES_ALL, $end_date));
        $allCdnttSocial = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $all = count($allCdnttSocial);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 29, $all == 0 ? 0 : ($target * 1.0 / $all) * 100);

        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 1, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::CDNTT], [FocusWork::SOCIAL]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 31, count($all[0]) == 0 ? 0 : (count($target) * 1.0 / count($all[0])) * 100);

        // Стилизация ячеек
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 29)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 29)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 31)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 31)->getStyle()->getAlignment()->setHorizontal('center');

        //-------------------------------------


        //Отдел Кванториум (тех. направленность)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::QUANT], [FocusWork::TECHNICAL], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        // Процент обучающихся в 2+ группах
        $target = count(SupportReportFunctions::GetDoubleParticipantsFromGroup(ReportConst::PROD, $targetGroups, ReportConst::AGES_ALL, $end_date));
        $allQuantTechnical = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $all = count($allQuantTechnical);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 33, $all == 0 ? 0 : ($target / $all) * 100);


        // Процент успешно защитивших проект (получивших сертификат)
        $target = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $allQuantTechnical));
        //$all = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date));

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 35, $all == 0 ? 0 : ($target * 1.0 / $all) * 100);


        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 1, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::QUANT], [FocusWork::TECHNICAL]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 36, count($all[0]) == 0 ? 0 : (count($target) * 1.0 / count($all[0])) * 100);

        // Стилизация ячеек
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 33)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 33)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 35)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 35)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 36)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 36)->getStyle()->getAlignment()->setHorizontal('center');

        //--------------------------------------


        //Отдел Моб. Кванториум (тех. направленность)

        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 1, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::MOB_QUANT], [FocusWork::TECHNICAL]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 39, count($all[0]) == 0 ? 0 : (count($target) * 1.0 / count($all[0])) * 100);

        // Стилизация ячеек
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 39)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 39)->getStyle()->getAlignment()->setHorizontal('center');

        //--------------------------------------


        //Отдел ЦОД (естес.-науч. направленность)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::COD], [FocusWork::SCIENCE], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        // Процент обучающихся в 2+ группах
        $target = count(SupportReportFunctions::GetDoubleParticipantsFromGroup(ReportConst::PROD, $targetGroups, ReportConst::AGES_ALL, $end_date));
        $allCodScience = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $all = count($allCodScience);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 49, $all == 0 ? 0 : ($target / $all) * 100);


        // Процент успешно защитивших проект (получивших сертификат)
        $target = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $allCodScience));
        //$all = count(SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date));

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 51, $all == 0 ? 0 : ($target * 1.0 / $all) * 100);


        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 1, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::COD], [FocusWork::SCIENCE]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 52, count($all[0]) == 0 ? 0 : (count($target) * 1.0 / count($all[0])) * 100);

        // Стилизация ячеек
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 49)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 49)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 51)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 51)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 52)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 52)->getStyle()->getAlignment()->setHorizontal('center');

        //--------------------------------------


        //Отдел ЦОД (худож. направленность)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::COD], [FocusWork::ART], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        // Процент обучающихся в 2+ группах
        $target = count(SupportReportFunctions::GetDoubleParticipantsFromGroup(ReportConst::PROD, $targetGroups, ReportConst::AGES_ALL, $end_date));
        $allCodArt = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $all = count($allCodArt);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 54, $all == 0 ? 0 : ($target / $all) * 100);


        // Процент успешно защитивших проект (получивших сертификат)
        $target = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $allCodArt));
        //$all = count(SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date));

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 56, $all == 0 ? 0 : ($target * 1.0 / $all) * 100);

        // Стилизация ячеек
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 54)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 54)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 56)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 56)->getStyle()->getAlignment()->setHorizontal('center');

        //--------------------------------------


        //Отдел ЦОД (тех. направленность - очная)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::COD], [FocusWork::TECHNICAL], [AllowRemoteWork::FULLTIME], [ReportConst::BUDGET]);

        // Процент обучающихся в 2+ группах
        $target = count(SupportReportFunctions::GetDoubleParticipantsFromGroup(ReportConst::PROD, $targetGroups, ReportConst::AGES_ALL, $end_date));
        $allCodTechnical = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $all = count($allCodTechnical);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 41, $all == 0 ? 0 : ($target / $all) * 100);


        // Процент успешно защитивших проект (получивших сертификат)
        $target = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $allCodTechnical));
        //$all = count(SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date));

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 43, $all == 0 ? 0 : ($target * 1.0 / $all) * 100);


        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 1, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::COD], [FocusWork::TECHNICAL], [AllowRemoteWork::FULLTIME]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 44, count($all[0]) == 0 ? 0 : (count($target) * 1.0 / count($all[0])) * 100);

        // Стилизация ячеек
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 41)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 41)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 43)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 43)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 44)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 44)->getStyle()->getAlignment()->setHorizontal('center');

        //--------------------------------------


        //Отдел ЦОД (тех. направленность - очная с дистантом)

        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 1, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::COD], [FocusWork::TECHNICAL], [AllowRemoteWork::FULLTIME_WITH_REMOTE]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 48, count($all[0]) == 0 ? 0 : (count($target) * 1.0 / count($all[0])) * 100);

        // Стилизация ячеек
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 48)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 48)->getStyle()->getAlignment()->setHorizontal('center');

        //---------------------------------------------------


        //Отдел ЦОД (физкул.-спортивная направленность)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::COD], [FocusWork::SPORT], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        // Процент обучающихся в 2+ группах
        $target = count(SupportReportFunctions::GetDoubleParticipantsFromGroup(ReportConst::PROD, $targetGroups, ReportConst::AGES_ALL, $end_date));
        $allCodSport = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $all = count($allCodSport);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 58, $all == 0 ? 0 : ($target / $all) * 100);

        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 1, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::COD], [FocusWork::SPORT]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 60, count($all[0]) == 0 ? 0 : (count($target) * 1.0 / count($all[0])) * 100);

        // Стилизация ячеек
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 58)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 58)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 60)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 60)->getStyle()->getAlignment()->setHorizontal('center');

        //---------------------------------------------



        /*
         * |----------------------------------------------------------------|
         * | Подсчет количества человеко-часов по отделам и направленностям |
         * |----------------------------------------------------------------|
         */

        //Отдел Технопарк (тех. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allTechoparkTechnical, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 8, count($visits));

        //---------------

        //Отдел ЦДНТТ (тех. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCdnttTechnical, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 9, count($visits));

        //---------------

        //Отдел ЦДНТТ (худ. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCdnttArt, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 10, count($visits));

        //---------------

        //Отдел ЦДНТТ (соц-пед. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCdnttSocial, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 11, count($visits));

        //---------------

        //Отдел Кванториум (тех. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allQuantTechnical, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 12, count($visits));

        //---------------

        //Отдел Моб. Кванториум (тех. направленность)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::MOB_QUANT], [FocusWork::TECHNICAL], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        $allMobQuantTechnical = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allMobQuantTechnical, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 13, count($visits));

        //---------------

        //Отдел ЦОД (тех. направленность - очная)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCodTechnical, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 14, count($visits));

        //---------------

        //Отдел ЦОД (тех. направленность - дистант)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::COD], [FocusWork::TECHNICAL], [AllowRemoteWork::FULLTIME_WITH_REMOTE], [ReportConst::BUDGET]);

        $allCodTechnicalRemote = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCodTechnicalRemote, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 15, count($visits));

        //---------------

        //Отдел ЦОД (естес.-науч. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCodScience, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 16, count($visits));

        //---------------

        //Отдел ЦОД (худож. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCodArt, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 17, count($visits));

        //---------------

        //Отдел ЦОД (физкульт.-спорт. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCodSport, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 18, count($visits));

        //---------------


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