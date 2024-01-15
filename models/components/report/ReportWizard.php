<?php

namespace app\models\components\report;

//--Класс для создания отчетов в формате MS Excel--
//----Бизнес-логика реализована в методах класса SupportReportFunctions
use app\models\components\ExcelWizard;
use app\models\work\AllowRemoteWork;
use app\models\work\AuditoriumWork;
use app\models\work\BranchWork;
use app\models\work\EventLevelWork;
use app\models\work\FocusWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\TeamWork;
use app\models\work\VisitWork;
use Codeception\PHPUnit\ResultPrinter\Report;
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
        $allTechoparkTechnicalUnique = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 1, ReportConst::AGES_ALL, $end_date);
        $allU = count($allTechoparkTechnicalUnique);
        $all = count($allTechoparkTechnical);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 16, $allU == 0 ? 0 : round(($target / $allU) * 100));


        // Процент успешно защитивших проект (получивших сертификат)
        $target = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $allTechoparkTechnical));
        //$all = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date));

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 18, $all == 0 ? 0 : round(($target * 1.0 / $all) * 100));


        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 0, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::TECHNO], [FocusWork::TECHNICAL]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all)[0];

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 19, count($all[0]) == 0 ? 0 : round((count($target) * 1.0 / count($all[0])) * 100));

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
        $allCdnttTechnicalUnique = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 1, ReportConst::AGES_ALL, $end_date);
        $allU = count($allCdnttTechnicalUnique);
        $all = count($allCdnttTechnical);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 21, $allU == 0 ? 0 : round(($target * 1.0 / $allU) * 100));

        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 0, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::CDNTT], [FocusWork::TECHNICAL]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all)[0];

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 23, count($all[0]) == 0 ? 0 : round((count($target) * 1.0 / count($all[0])) * 100));

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
        $allCdnttArtUnique = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 1, ReportConst::AGES_ALL, $end_date);
        $allU = count($allCdnttArtUnique);
        $all = count($allCdnttArt);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 25, $allU == 0 ? 0 : round(($target * 1.0 / $allU) * 100));

        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 0, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::CDNTT], [FocusWork::ART]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all)[0];

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 27, count($all[0]) == 0 ? 0 : round((count($target) * 1.0 / count($all[0])) * 100));

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
        $allCdnttSocialUnique = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 1, ReportConst::AGES_ALL, $end_date);
        $allU = count($allCdnttSocialUnique);
        $all = count($allCdnttSocial);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 29, $allU == 0 ? 0 : round(($target * 1.0 / $allU) * 100));

        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 0, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::CDNTT], [FocusWork::SOCIAL]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all)[0];

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 31, count($all[0]) == 0 ? 0 : round((count($target) * 1.0 / count($all[0])) * 100));

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
        $allQuantTechnicalUnique = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 1, ReportConst::AGES_ALL, $end_date);
        $allU = count($allQuantTechnicalUnique);
        $all = count($allQuantTechnical);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 33, $allU == 0 ? 0 : round(($target / $allU) * 100));


        // Процент успешно защитивших проект (получивших сертификат)
        $target = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $allQuantTechnical));
        //$all = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date));

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 35, $all == 0 ? 0 : round(($target * 1.0 / $all) * 100));


        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 0, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::QUANT], [FocusWork::TECHNICAL]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all)[0];

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 36, count($all[0]) == 0 ? 0 : round((count($target) * 1.0 / count($all[0])) * 100));

        // Стилизация ячеек
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 33)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 33)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 35)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 35)->getStyle()->getAlignment()->setHorizontal('center');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 36)->getStyle()->getAlignment()->setVertical('top');
        $inputData->getSheet(1)->getCellByColumnAndRow(10, 36)->getStyle()->getAlignment()->setHorizontal('center');

        //--------------------------------------


        //Отдел Моб. Кванториум (тех. направленность)

        // Процент успешно защитивших проект (получивших сертификат)
        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::MOB_QUANT], [FocusWork::TECHNICAL], AllowRemoteWork::ALL, [ReportConst::BUDGET]);
        $allMobTechnical = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $allMobTechnicalUnique = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 1, ReportConst::AGES_ALL, $end_date);
        $all = count($allMobTechnical);

        $target = SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $allMobTechnical);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 39, $all == 0 ? 0 : round(count($target) * 1.0 / $all * 100));

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

        //-------------------------------

        /*foreach ($targetGroups as $group)
        {
            $tgps = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, [$group], 0, ReportConst::AGES_ALL, $end_date);
            $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $tgps, $start_date, $end_date, $visit_type);
            var_dump( $group->number.' '.count($visits).'<br>');
        }*/


        //-------------------------------

        $allCodScienceUnique = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 1, ReportConst::AGES_ALL, $end_date);
        $allU = count($allCodScienceUnique);
        $all = count($allCodScience);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 49, $all == 0 ? 0 : round(($target / $allU) * 100));


        // Процент успешно защитивших проект (получивших сертификат)
        $target = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $allCodScience));
        //$all = count(SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date));

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 51, $all == 0 ? 0 : round(($target * 1.0 / $all) * 100));


        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 0, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::COD], [FocusWork::SCIENCE]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all)[0];

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 52, count($all[0]) == 0 ? 0 : round((count($target) * 1.0 / count($all[0])) * 100));

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
        $allCodArtUnique = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 1, ReportConst::AGES_ALL, $end_date);
        $allU = count($allCodArtUnique);
        $all = count($allCodArt);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 54, $all == 0 ? 0 : round(($target / $allU) * 100));


        // Процент успешно защитивших проект (получивших сертификат)
        $target = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $allCodArt));
        //$all = count(SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date));

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 56, $all == 0 ? 0 : round(($target * 1.0 / $all) * 100));

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



        $allCodTechnicalUnique = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 1, ReportConst::AGES_ALL, $end_date);
        $allU = count($allCodTechnicalUnique);
        $all = count($allCodTechnical);


        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 41, $all == 0 ? 0 : round(($target / $allU) * 100));


        // Процент успешно защитивших проект (получивших сертификат)
        $target = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $allCodTechnical));
        //$all = count(SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date));

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 43, $all == 0 ? 0 : round(($target * 1.0 / $all) * 100));


        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 0, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::COD], [FocusWork::TECHNICAL], [AllowRemoteWork::FULLTIME]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all)[0];

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 44, count($all[0]) == 0 ? 0 : round((count($target) * 1.0 / count($all[0])) * 100));

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


        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::COD], [FocusWork::TECHNICAL], [AllowRemoteWork::FULLTIME_WITH_REMOTE], [ReportConst::BUDGET]);

        // Процент успешно защитивших проект (получивших сертификат) [это неправда, но это так]
        $allCodTechnicalRemote = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $all = count($allCodTechnicalRemote);

        $target = count(SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::PROD, $allCodTechnicalRemote));


        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 48, $all == 0 ? 0 : round(($target * 1.0 / $all) * 100));

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
        $allCodSportUnique = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 1, ReportConst::AGES_ALL, $end_date);
        $allU = count($allCodSportUnique);
        $all = count($allCodSport);

        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 58, $all == 0 ? 0 : round(($target * 1.0 / $allU) * 100));

        // Процент победителей и призеров от общего числа участников
        $all = SupportReportFunctions::GetParticipants(ReportConst::PROD, $start_date, $end_date, 0, 0,
            [EventLevelWork::REGIONAL, EventLevelWork::FEDERAL, EventLevelWork::INTERNATIONAL],
            [BranchWork::COD], [FocusWork::SPORT]);
        $target = SupportReportFunctions::GetParticipantAchievements(ReportConst::PROD, $all)[0];


        $inputData->getSheet(1)->setCellValueByColumnAndRow(10, 60, count($all[0]) == 0 ? 0 : round((count($target) * 1.0 / count($all[0])) * 100));

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

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allTechoparkTechnical, $start_date, $end_date, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 8, count($visits));

        //---------------

        //Отдел ЦДНТТ (тех. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCdnttTechnical, $start_date, $end_date, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 9, count($visits));

        //---------------

        //Отдел ЦДНТТ (худ. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCdnttArt, $start_date, $end_date, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 10, count($visits));

        //---------------

        //Отдел ЦДНТТ (соц-пед. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCdnttSocial, $start_date, $end_date, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 11, count($visits));

        //---------------

        //Отдел Кванториум (тех. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allQuantTechnical, $start_date, $end_date, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 12, count($visits));

        //---------------

        //Отдел Моб. Кванториум (тех. направленность)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::MOB_QUANT], [FocusWork::TECHNICAL], AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        $allMobQuantTechnical = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allMobQuantTechnical, $start_date, $end_date, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 13, count($visits));

        //---------------

        //Отдел ЦОД (тех. направленность - очная)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCodTechnical, $start_date, $end_date, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 14, count($visits));

        //---------------

        //Отдел ЦОД (тех. направленность - дистант)

        $targetGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            [BranchWork::COD], [FocusWork::TECHNICAL], [AllowRemoteWork::FULLTIME_WITH_REMOTE], [ReportConst::BUDGET]);

        $allCodTechnicalRemote = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $targetGroups, 0, ReportConst::AGES_ALL, $end_date);
        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCodTechnicalRemote, $start_date, $end_date, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 15, count($visits));

        //---------------

        //Отдел ЦОД (естес.-науч. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCodScience, $start_date, $end_date, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 16, count($visits));

        //---------------

        //Отдел ЦОД (худож. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCodArt, $start_date, $end_date, $visit_type);

        $inputData->getSheet(2)->setCellValueByColumnAndRow(10, 17, count($visits));

        //---------------

        //Отдел ЦОД (физкульт.-спорт. направленность)

        $visits = SupportReportFunctions::GetVisits(ReportConst::PROD, $allCodSport, $start_date, $end_date, $visit_type);

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
    //----------------------------------

    //--Функция генерации отчета ДОД--
    static public function GenerateDod($start_date, $end_date)
    {
        $inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/templates/report_DOD.xlsx');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/templates/report_DOD.xlsx');

        //----------------
        //--Раздел 3,4,5--
        //----------------

        //--Техническая направленность--
        $technicalGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::TECHNICAL]);
        $technicalAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $technicalGroups, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $technicalFemale = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $technicalGroups, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01', [ReportConst::FEMALE]);

        $technicalNetworkGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            BranchWork::ALL, [FocusWork::TECHNICAL], AllowRemoteWork::ALL,
            ReportConst::BUDGET_ALL, [], ReportConst::ALL_DATE_SELECTION, [ReportConst::NETWORK]);
        $technicalNetworkAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $technicalNetworkGroups);

        $technicalRemoteGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            BranchWork::ALL, [FocusWork::TECHNICAL], [AllowRemoteWork::FULLTIME_WITH_REMOTE]);
        $technicalRemoteAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $technicalRemoteGroups);
        //------------------------------

        //--Естественнонаучное направленность--
        $scienceGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::SCIENCE]);
        $scienceAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $scienceGroups, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $scienceFemale = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $scienceGroups, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01', [ReportConst::FEMALE]);

        $scienceNetworkGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            BranchWork::ALL, [FocusWork::SCIENCE], AllowRemoteWork::ALL,
            ReportConst::BUDGET_ALL, [], ReportConst::ALL_DATE_SELECTION, [ReportConst::NETWORK]);
        $scienceNetworkAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $scienceNetworkGroups);

        $scienceRemoteGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            BranchWork::ALL, [FocusWork::SCIENCE], [AllowRemoteWork::FULLTIME_WITH_REMOTE]);
        $scienceRemoteAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $scienceRemoteGroups);
        //-------------------------------------

        //--Соц-пед направленность--
        $socialGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::SOCIAL]);
        $socialAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $socialGroups, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $socialFemale = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $socialGroups, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01', [ReportConst::FEMALE]);

        $socialNetworkGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            BranchWork::ALL, [FocusWork::SOCIAL], AllowRemoteWork::ALL,
            ReportConst::BUDGET_ALL, [], ReportConst::ALL_DATE_SELECTION, [ReportConst::NETWORK]);
        $socialNetworkAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $socialNetworkGroups);

        $socialRemoteGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            BranchWork::ALL, [FocusWork::SOCIAL], [AllowRemoteWork::FULLTIME_WITH_REMOTE]);
        $socialRemoteAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $socialRemoteGroups);
        //--------------------------

        //--Художественная направленность--
        $artGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::ART]);
        $artAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $artGroups, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $artFemale = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $artGroups, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01', [ReportConst::FEMALE]);

        $artNetworkGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            BranchWork::ALL, [FocusWork::ART], AllowRemoteWork::ALL,
            ReportConst::BUDGET_ALL, [], ReportConst::ALL_DATE_SELECTION, [ReportConst::NETWORK]);
        $artNetworkAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $artNetworkGroups);

        $artRemoteGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            BranchWork::ALL, [FocusWork::ART], [AllowRemoteWork::FULLTIME_WITH_REMOTE]);
        $artRemoteAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $artRemoteGroups);
        //---------------------------------

        //--Спортивная направленность--
        $sportGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::SPORT]);
        $sportAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $sportGroups, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $sportFemale = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $sportGroups, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01', [ReportConst::FEMALE]);

        $sportNetworkGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            BranchWork::ALL, [FocusWork::SPORT], AllowRemoteWork::ALL,
            ReportConst::BUDGET_ALL, [], ReportConst::ALL_DATE_SELECTION, [ReportConst::NETWORK]);
        $sportNetworkAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $sportNetworkGroups);

        $sportRemoteGroups = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date,
            BranchWork::ALL, [FocusWork::SPORT], [AllowRemoteWork::FULLTIME_WITH_REMOTE]);
        $sportRemoteAll = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $sportRemoteGroups);
        //-----------------------------


        //--Заполнение раздела 3--
        $inputData->getSheet(0)->setCellValueByColumnAndRow(2, 8, count($technicalAll));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(2, 9, count($scienceAll));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(2, 11, count($socialAll));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(2, 12, count($artAll));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(2, 14, count($sportAll));

        $inputData->getSheet(0)->setCellValueByColumnAndRow(3, 8, count($technicalFemale));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(3, 9, count($scienceFemale));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(3, 11, count($socialFemale));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(3, 12, count($artFemale));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(3, 14, count($sportFemale));

        $inputData->getSheet(0)->setCellValueByColumnAndRow(4, 8, count($technicalNetworkAll));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(4, 9, count($scienceNetworkAll));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(4, 11, count($socialNetworkAll));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(4, 12, count($artNetworkAll));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(4, 14, count($sportNetworkAll));

        $inputData->getSheet(0)->setCellValueByColumnAndRow(5, 8, count($technicalRemoteAll));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(5, 9, count($scienceRemoteAll));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(5, 11, count($socialRemoteAll));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(5, 12, count($artRemoteAll));
        $inputData->getSheet(0)->setCellValueByColumnAndRow(5, 14, count($sportRemoteAll));
        //------------------------


        //--Заполнение раздела 4--

        //--Суммы по возрастам--
        $sumAge12 = 0;
        $sumAges = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];
        //----------------------


        //--Разбиваем детей по возрастам (техническая направленность)--
        $ageParticipants12 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $technicalGroups, 0, [0, 1, 2], ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 6, count($ageParticipants12));
        $sumAge12 += count($ageParticipants12);

        for ($i = 3; $i < 18; $i++)
        {
            $ageParticipants = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $technicalGroups, 0, [$i], ((int)explode("-", $start_date)[0] + 1).'-01-01');
            $inputData->getSheet(1)->setCellValueByColumnAndRow($i + 1, 6, count($ageParticipants));
            $sumAges[$i] += count($ageParticipants);
        }
        //-------------------------------------------------------------

        //--Разбиваем детей по возрастам (естественнонаучная направленность)--
        $ageParticipants12 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $scienceGroups, 0, [0, 1, 2], ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 7, count($ageParticipants12));
        $sumAge12 += count($ageParticipants12);

        for ($i = 3; $i < 18; $i++)
        {
            $ageParticipants = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $scienceGroups, 0, [$i], ((int)explode("-", $start_date)[0] + 1).'-01-01');
            $inputData->getSheet(1)->setCellValueByColumnAndRow($i + 1, 7, count($ageParticipants));
            $sumAges[$i] += count($ageParticipants);
        }
        //--------------------------------------------------------------------

        //--Разбиваем детей по возрастам (соц-пед направленность)--
        $ageParticipants12 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $socialGroups, 0, [0, 1, 2], ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 9, count($ageParticipants12));
        $sumAge12 += count($ageParticipants12);

        for ($i = 3; $i < 18; $i++)
        {
            $ageParticipants = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $socialGroups, 0, [$i], ((int)explode("-", $start_date)[0] + 1).'-01-01');
            $inputData->getSheet(1)->setCellValueByColumnAndRow($i + 1, 9, count($ageParticipants));
            $sumAges[$i] += count($ageParticipants);
        }
        //---------------------------------------------------------

        //--Разбиваем детей по возрастам (художественная направленность)--
        $ageParticipants12 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $artGroups, 0, [0, 1, 2], ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 10, count($ageParticipants12));
        $sumAge12 += count($ageParticipants12);

        for ($i = 3; $i < 18; $i++)
        {
            $ageParticipants = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $artGroups, 0, [$i], ((int)explode("-", $start_date)[0] + 1).'-01-01');
            $inputData->getSheet(1)->setCellValueByColumnAndRow($i + 1, 10, count($ageParticipants));
            $sumAges[$i] += count($ageParticipants);
        }
        //-----------------------------------------------------------------

        //--Разбиваем детей по возрастам (спортивная направленность)--
        $ageParticipants12 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $sportGroups, 0, [0, 1, 2], ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 12, count($ageParticipants12));
        $sumAge12 += count($ageParticipants12);

        for ($i = 3; $i < 18; $i++)
        {
            $ageParticipants = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $sportGroups, 0, [$i], ((int)explode("-", $start_date)[0] + 1).'-01-01');
            $inputData->getSheet(1)->setCellValueByColumnAndRow($i + 1, 12, count($ageParticipants));
            $sumAges[$i] += count($ageParticipants);
        }
        //------------------------------------------------------------


        //--Заполняем суммы по возрастам--
        $inputData->getSheet(1)->setCellValueByColumnAndRow(3, 12, $sumAge12);

        for ($i = 3; $i < 18; $i++)
            $inputData->getSheet(1)->setCellValueByColumnAndRow($i + 1, 16, $sumAges[$i]);
        //--------------------------------

        //--Заполняем суммы по направленностям--
        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 6, count($technicalAll));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 7, count($scienceAll));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 9, count($socialAll));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 10, count($artAll));
        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 12, count($sportAll));

        $inputData->getSheet(1)->setCellValueByColumnAndRow(2, 16, count($technicalAll) + count($scienceAll) + count($socialAll) + count($artAll) + count($sportAll));
        //--------------------------------------

        //------------------------


        //--Заполнение раздела 5--

        $technicalGroupsBudget = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::TECHNICAL], AllowRemoteWork::ALL, [ReportConst::BUDGET]);
        $technicalGroupsComm = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::TECHNICAL], AllowRemoteWork::ALL, [ReportConst::COMMERCIAL]);
        $budgetTechnical = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $technicalGroupsBudget, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $commTechnical = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $technicalGroupsComm, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');

        $scienceGroupsBudget = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::SCIENCE], AllowRemoteWork::ALL, [ReportConst::BUDGET]);
        $scienceGroupsComm = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::SCIENCE], AllowRemoteWork::ALL, [ReportConst::COMMERCIAL]);
        $budgetScience = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $scienceGroupsBudget, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $commScience = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $scienceGroupsComm, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');

        $socialGroupsBudget = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::SOCIAL], AllowRemoteWork::ALL, [ReportConst::BUDGET]);
        $socialGroupsComm = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::SOCIAL], AllowRemoteWork::ALL, [ReportConst::COMMERCIAL]);
        $budgetSocial = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $socialGroupsBudget, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $commSocial = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $socialGroupsComm, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');

        $artGroupsBudget = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::ART], AllowRemoteWork::ALL, [ReportConst::BUDGET]);
        $artGroupsComm = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::ART], AllowRemoteWork::ALL, [ReportConst::COMMERCIAL]);
        $budgetArt = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $artGroupsBudget, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $commArt = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $artGroupsComm, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');

        $sportGroupsBudget = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::SPORT], AllowRemoteWork::ALL, [ReportConst::BUDGET]);
        $sportGroupsComm = SupportReportFunctions::GetTrainingGroups(ReportConst::PROD, $start_date, $end_date, BranchWork::ALL, [FocusWork::SPORT], AllowRemoteWork::ALL, [ReportConst::COMMERCIAL]);
        $budgetSport = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $sportGroupsBudget, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');
        $commSport = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::PROD, $sportGroupsComm, 0, ReportConst::AGES_ALL_18, ((int)explode("-", $start_date)[0] + 1).'-01-01');


        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 6, count($budgetTechnical));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 6, count($commTechnical));

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 7, count($budgetScience));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 7, count($commScience));

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 9, count($budgetSocial));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 9, count($commSocial));

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 10, count($budgetArt));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 10, count($commArt));

        $inputData->getSheet(2)->setCellValueByColumnAndRow(3, 12, count($budgetSport));
        $inputData->getSheet(2)->setCellValueByColumnAndRow(5, 12, count($commSport));

        //------------------------


        //--Заполнение раздела 10--

        //--Получаем все помещения--

        $audsAll = AuditoriumWork::find()/*->where(['branch_id' => 3])*/;

        //--Получаем все помещения не в собственности--

        $audsRent = AuditoriumWork::find()->where(['!=', 'branch_id', 3]);

        //----Ищем лаборатории--

        $labs = (clone $audsAll)->andWhere(['auditorium_type_id' => 1])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(2, 13, count($labs) > 0 ? 1 : 2);

        $labs = (clone $audsRent)->andWhere(['auditorium_type_id' => 1])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 13, count($labs) > 0 ? 1 : 2);

        //----------------------

        //----Ищем мастерские--

        $work = (clone $audsAll)->andWhere(['auditorium_type_id' => 2])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(2, 14, count($work) > 0 ? 1 : 2);

        $work = (clone $audsRent)->andWhere(['auditorium_type_id' => 2])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 14, count($work) > 0 ? 1 : 2);

        //---------------------

        //----Ищем учебные классы--

        $stud = (clone $audsAll)->andWhere(['auditorium_type_id' => 3])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(2, 12, count($stud) > 0 ? 1 : 2);

        $stud = (clone $audsRent)->andWhere(['auditorium_type_id' => 3])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 12, count($stud) > 0 ? 1 : 2);

        //-------------------------

        //----Ищем лекционные аудитории--

        $lec = (clone $audsAll)->andWhere(['auditorium_type_id' => 4])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(2, 20, count($lec) > 0 ? 1 : 2);

        $lec = (clone $audsRent)->andWhere(['auditorium_type_id' => 4])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 20, count($lec) > 0 ? 1 : 2);

        //-------------------------------

        //----Ищем компьютерные кабинеты--

        $lec = (clone $audsAll)->andWhere(['auditorium_type_id' => 5])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(2, 21, count($lec) > 0 ? 1 : 2);

        $lec = (clone $audsRent)->andWhere(['auditorium_type_id' => 5])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 21, count($lec) > 0 ? 1 : 2);

        //--------------------------------

        //----Ищем актовые залы--

        $lec = (clone $audsAll)->andWhere(['auditorium_type_id' => 6])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(2, 8, count($lec) > 0 ? 1 : 2);

        $lec = (clone $audsRent)->andWhere(['auditorium_type_id' => 6])->all();
        $inputData->getSheet(3)->setCellValueByColumnAndRow(3, 8, count($lec) > 0 ? 1 : 2);

        //-----------------------

        //-------------------------


        //--Заполнение раздела 10--

        //--Получаем все аудитории--

        $auds = AuditoriumWork::find()->where(['include_square' => 1]);

        //--Считаем площадь помещений--

        $sumArea = 0.0;
        $sumStudyArea = 0.0;
        $sumRentArea = 0.0;
        $sumRentStudyArea = 0.0;
        $sumOperationArea = 0.0;
        $sumOperationStudyArea = 0.0;

        $audsAll = (clone $auds)->all();
        foreach ($audsAll as $aud)
        {
            $sumArea += $aud->square;
            if ($aud->is_education == 1) $sumStudyArea += $aud->square;

            if ($aud->branch_id == 3 || $aud->branch_id == 8)
            {
                $sumOperationArea += $aud->square;
                if ($aud->is_education == 1) $sumOperationStudyArea += $aud->square;
            }
            else /*if ($aud->branch_id == 1 || $aud->branch_id == 2)*/
            {
                $sumRentArea += $aud->square;
                if ($aud->is_education == 1) $sumRentStudyArea += $aud->square;
            }

        }

        $inputData->getSheet(4)->setCellValueByColumnAndRow(2, 8, $sumArea);
        $inputData->getSheet(4)->setCellValueByColumnAndRow(5, 8, $sumOperationArea);
        $inputData->getSheet(4)->setCellValueByColumnAndRow(6, 8, $sumRentArea);

        $inputData->getSheet(4)->setCellValueByColumnAndRow(2, 9, $sumStudyArea);
        $inputData->getSheet(4)->setCellValueByColumnAndRow(5, 9, $sumOperationStudyArea);
        $inputData->getSheet(4)->setCellValueByColumnAndRow(6, 9, $sumRentStudyArea);

        //--------------------------

        //-------------------------



        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="report.xlsx"');
        header('Cache-Control: max-age=0');
        mb_internal_encoding('Windows-1251');
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel2007');
        $writer->save('php://output');
        exit;
    }
    //--------------------------------



}