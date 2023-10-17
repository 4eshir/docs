<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands\report_test;

use app\commands\SupCommandsController;
use app\models\common\EventLevel;
use app\models\common\Focus;
use app\models\common\ForeignEventParticipants;
use app\models\components\report\ReportConst;
use app\models\components\report\SupportReportFunctions;
use app\models\LoginForm;
use app\models\test\common\GetParticipantsTeam;
use app\models\test\work\GetParticipantsTeamWork;
use app\models\work\AllowRemoteWork;
use app\models\work\BranchWork;
use app\models\work\EventLevelWork;
use app\models\work\FocusWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\ForeignEventWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\VisitWork;
use Mpdf\Tag\Br;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Console;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class ReportTestController extends Controller
{

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    //--Запуск ВСЕХ тестов модуля--
    public function actionTestAll()
    {
        $this->actionParticipantTest();
        $this->actionGroupTest();
    }
    //-----------------------------



    //--Экшн и вспомогательные функции для тестирования участников мероприятий--
    public function actionParticipantTest()
    {
        $this->stdout("\n\n| Foreign event participants and achievements tests\n", Console::FG_CYAN);
        $this->GetParticipantsTest(); //Тест на выгрузку участников деятельности по заданным параметрам
        $this->stdout("\n");
        $this->ParticipantAchievementsTest(); //Тест на выгрузку победителей и призеров по заданным параметрам
        $this->stdout("\n|".str_repeat("-", 50)."\n", Console::FG_CYAN);
    }


    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    private function GetParticipantsTest()
    {
        $this->stdout("\n------(Get_Participants tests)------\n|".str_repeat(" ", 34)."|\n", Console::FG_PURPLE);

        $testResult1 = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2020-01-01', '2023-01-01', 0);
        $testResult2 = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2020-01-01', '2023-01-01', 1);
        $testResult3 = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2020-01-01', '2023-01-01', 0, 1);
        $testResult4 = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2020-01-01', '2023-01-01', 1, 1);
        $testResult5 = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2020-01-01', '2023-01-01', 1, 0, [EventLevelWork::INTERNAL]);
        $testResult6 = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2020-01-01', '2023-01-01', 1, 1, [EventLevelWork::INTERNAL]);
        $testResult7 = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2020-01-01', '2023-01-01', 1, 0, [EventLevelWork::INTERNAL], [BranchWork::CDNTT]);
        $testResult8 = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2022-02-01', '2023-01-01', 0, 0, EventLevelWork::ALL, [BranchWork::TECHNO, BranchWork::COD]);
        $testResult9 = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2022-01-01', '2023-01-01', 1, 0, EventLevelWork::ALL, BranchWork::ALL, [FocusWork::ART, FocusWork::SPORT]);
        $testResult10 = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2022-01-01', '2022-01-30', 0, 1, EventLevelWork::ALL, BranchWork::ALL, [FocusWork::TECHNICAL], AllowRemoteWork::ALL);


        $expectedResult1 = [[1, 1, 2, 2, 3, 3, 4, 5, 6, 7, 8], [], 11];
        $expectedResult2 = [[2, 2, 3, 5, 6, 7, 8], [1, 2], 9];
        $expectedResult3 = [[1, 2, 3, 4, 5, 6, 7, 8], [], 8];
        $expectedResult4 = [[2, 3, 5, 6, 7, 8], [1, 2], 8];
        $expectedResult5 = [[2, 3], [1], 3];
        $expectedResult6 = [[2, 3], [1], 3];
        $expectedResult7 = [[], [1], 1];
        $expectedResult8 = [[1, 6, 7, 8], [], 4];
        $expectedResult9 = [[2, 3, 5], [], 3];
        $expectedResult10 = [[1, 4], [], 2];

        if ($testResult1[0] === $expectedResult1[0] &&
            $testResult1[1] === $expectedResult1[1] &&
            $testResult1[2] == $expectedResult1[2])
            $this->stdout('| Test #1 was passed successfully  |'."\n", Console::FG_GREEN);
        else
        {
            $this->stdout('| Test #1 failed                   |'."\n", Console::FG_RED);
            $this->stdout($testResult1[0] === $expectedResult1[0] ? "T1 OK\n" : "T1 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult1[1] === $expectedResult1[1] ? "T2 OK\n" : "T2 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult1[2] == $expectedResult1[2] ? "T3 OK\n" : "T3 FAIL\n", Console::FG_YELLOW);
        }

        if ($testResult2[0] === $expectedResult2[0] &&
            $testResult2[1] === $expectedResult2[1] &&
            $testResult2[2] == $expectedResult2[2])
            $this->stdout('| Test #2 was passed successfully  |'."\n", Console::FG_GREEN);
        else
        {
            $this->stdout('| Test #2 failed                   |'."\n", Console::FG_RED);
            $this->stdout($testResult2[0] === $expectedResult2[0] ? "T1 OK\n" : "T1 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult2[1] === $expectedResult2[1] ? "T2 OK\n" : "T2 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult2[2] == $expectedResult2[2] ? "T3 OK\n" : "T3 FAIL\n", Console::FG_YELLOW);

            $this->stdout(count($testResult2[1])."\n", Console::FG_CYAN);
            foreach ($testResult2[4] as $one)
                $this->stdout($one."\n", Console::FG_CYAN);
        }

        if ($testResult3[0] === $expectedResult3[0] &&
            $testResult3[1] === $expectedResult3[1] &&
            $testResult3[2] == $expectedResult3[2])
            $this->stdout('| Test #3 was passed successfully  |'."\n", Console::FG_GREEN);
        else
        {
            $this->stdout('| Test #3 failed                   |'."\n", Console::FG_RED);
            $this->stdout($testResult3[0] === $expectedResult3[0] ? "T1 OK\n" : "T1 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult3[1] === $expectedResult3[1] ? "T2 OK\n" : "T2 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult3[2] == $expectedResult3[2] ? "T3 OK\n" : "T3 FAIL\n", Console::FG_YELLOW);
        }

        if ($testResult4[0] === $expectedResult4[0] &&
            $testResult4[1] === $expectedResult4[1] &&
            $testResult4[2] == $expectedResult4[2])
            $this->stdout('| Test #4 was passed successfully  |'."\n", Console::FG_GREEN);
        else
        {
            $this->stdout('| Test #4 failed                  |'."\n", Console::FG_RED);
            $this->stdout($testResult4[0] === $expectedResult4[0] ? "T1 OK\n" : "T1 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult4[1] === $expectedResult4[1] ? "T2 OK\n" : "T2 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult4[2] == $expectedResult4[2] ? "T3 OK\n" : "T3 FAIL\n", Console::FG_YELLOW);

            foreach ($testResult4[3] as $one)
                $this->stdout($one."\n", Console::FG_YELLOW);
        }

        if ($testResult5[0] === $expectedResult5[0] &&
            $testResult5[1] === $expectedResult5[1] &&
            $testResult5[2] == $expectedResult5[2])
            $this->stdout('| Test #5 was passed successfully  |'."\n", Console::FG_GREEN);
        else
        {
            $this->stdout('| Test #5 failed                  |'."\n", Console::FG_RED);
            $this->stdout($testResult5[0] === $expectedResult5[0] ? "T1 OK\n" : "T1 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult5[1] === $expectedResult5[1] ? "T2 OK\n" : "T2 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult5[2] == $expectedResult5[2] ? "T3 OK\n" : "T3 FAIL\n", Console::FG_YELLOW);
        }

        if ($testResult6[0] === $expectedResult6[0] &&
            $testResult6[1] === $expectedResult6[1] &&
            $testResult6[2] == $expectedResult6[2])
            $this->stdout('| Test #6 was passed successfully  |'."\n", Console::FG_GREEN);
        else
        {
            $this->stdout('| Test #6 failed                   |'."\n", Console::FG_RED);
            $this->stdout($testResult6[0] === $expectedResult6[0] ? "T1 OK\n" : "T1 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult6[1] === $expectedResult6[1] ? "T2 OK\n" : "T2 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult6[2] == $expectedResult6[2] ? "T3 OK\n" : "T3 FAIL\n", Console::FG_YELLOW);
        }

        if ($testResult7[0] === $expectedResult7[0] &&
            $testResult7[1] === $expectedResult7[1] &&
            $testResult7[2] == $expectedResult7[2])
            $this->stdout('| Test #7 was passed successfully  |'."\n", Console::FG_GREEN);
        else
        {
            $this->stdout('| Test #7 failed                   |'."\n", Console::FG_RED);
            $this->stdout($testResult7[0] === $expectedResult7[0] ? "T1 OK\n" : "T1 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult7[1] === $expectedResult7[1] ? "T2 OK\n" : "T2 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult7[2] == $expectedResult7[2] ? "T3 OK\n" : "T3 FAIL\n", Console::FG_YELLOW);
        }

        if ($testResult8[0] === $expectedResult8[0] &&
            $testResult8[1] === $expectedResult8[1] &&
            $testResult8[2] == $expectedResult8[2])
            $this->stdout('| Test #8 was passed successfully  |'."\n", Console::FG_GREEN);
        else
        {
            $this->stdout('| Test #8 failed                   |'."\n", Console::FG_RED);
            $this->stdout($testResult8[0] === $expectedResult8[0] ? "T1 OK\n" : "T1 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult8[1] === $expectedResult8[1] ? "T2 OK\n" : "T2 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult8[2] == $expectedResult8[2] ? "T3 OK\n" : "T3 FAIL\n", Console::FG_YELLOW);
        }

        if ($testResult9[0] === $expectedResult9[0] &&
            $testResult9[1] === $expectedResult9[1] &&
            $testResult9[2] == $expectedResult9[2])
            $this->stdout('| Test #9 was passed successfully  |'."\n", Console::FG_GREEN);
        else
        {
            $this->stdout('| Test #9 failed                   |'."\n", Console::FG_RED);
            $this->stdout($testResult9[0] === $expectedResult9[0] ? "T1 OK\n" : "T1 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult9[1] === $expectedResult9[1] ? "T2 OK\n" : "T2 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult9[2] == $expectedResult9[2] ? "T3 OK\n" : "T3 FAIL\n", Console::FG_YELLOW);

            foreach ($testResult9[1] as $one)
                $this->stdout($one."\n", Console::FG_YELLOW);
        }

        if ($testResult10[0] === $expectedResult10[0] &&
            $testResult10[1] === $expectedResult10[1] &&
            $testResult10[2] == $expectedResult10[2])
            $this->stdout('| Test #10 was passed successfully |'."\n", Console::FG_GREEN);
        else
        {
            $this->stdout('| Test #10 failed                  |'."\n", Console::FG_RED);
            $this->stdout($testResult10[0] === $expectedResult10[0] ? "T1 OK\n" : "T1 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult10[1] === $expectedResult10[1] ? "T2 OK\n" : "T2 FAIL\n", Console::FG_YELLOW);
            $this->stdout($testResult10[2] == $expectedResult10[2] ? "T3 OK\n" : "T3 FAIL\n", Console::FG_YELLOW);
        }


        $this->stdout(str_repeat("-", 36), Console::FG_PURPLE);



        return ExitCode::OK;
    }

    private function ParticipantAchievementsTest()
    {
        $this->stdout("\n----------(Achieves tests)----------\n|".str_repeat(" ", 34)."|\n", Console::FG_PURPLE);

        $participants = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2020-01-01', '2023-01-01', 0);
        $testResult1 = SupportReportFunctions::GetParticipantAchievements(ReportConst::TEST, $participants);
        $testResult2 = SupportReportFunctions::GetParticipantAchievements(ReportConst::TEST, $participants, 0, ParticipantAchievementWork::WINNER);
        $testResult3 = SupportReportFunctions::GetParticipantAchievements(ReportConst::TEST, $participants, 0, ParticipantAchievementWork::PRIZE);

        $participants = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2020-01-01', '2023-01-01', 1);
        $testResult4 = SupportReportFunctions::GetParticipantAchievements(ReportConst::TEST, $participants);
        $testResult5 = SupportReportFunctions::GetParticipantAchievements(ReportConst::TEST, $participants, 0, ParticipantAchievementWork::WINNER);
        $testResult6 = SupportReportFunctions::GetParticipantAchievements(ReportConst::TEST, $participants, 0, ParticipantAchievementWork::PRIZE);

        $participants = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2020-01-01', '2023-01-01', 1, 0, EventLevelWork::ALL, BranchWork::ALL, [FocusWork::TECHNICAL, FocusWork::ART]);
        $testResult7 = SupportReportFunctions::GetParticipantAchievements(ReportConst::TEST, $participants);

        $participants = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2020-01-01', '2023-01-01', 1, 1);
        $testResult8 = SupportReportFunctions::GetParticipantAchievements(ReportConst::TEST, $participants);

        $expectedResult1 = [2, 4, 6, 7];
        $expectedResult2 = [4, 6];
        $expectedResult3 = [2, 7];
        $expectedResult4 = [2, 4, 6, 7, 8, 9];
        $expectedResult5 = [4, 6, 8];
        $expectedResult6 = [2, 7, 9];
        $expectedResult7 = [2, 6, 7, 8, 9];
        $expectedResult8 = [2, 6, 7, 8, 9];



        if ($testResult1[0] == $expectedResult1)
            $this->stdout('| Test #1 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #1 failed                   |'."\n", Console::FG_RED);

        if ($testResult2[0] == $expectedResult2)
            $this->stdout('| Test #2 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #2 failed                   |'."\n", Console::FG_RED);

        if ($testResult3[0] == $expectedResult3)
            $this->stdout('| Test #3 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #3 failed                   |'."\n", Console::FG_RED);

        if ($testResult4[0] == $expectedResult4)
            $this->stdout('| Test #4 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #4 failed                   |'."\n", Console::FG_RED);

        if ($testResult5[0] == $expectedResult5)
            $this->stdout('| Test #5 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #5 failed                   |'."\n", Console::FG_RED);

        if ($testResult6[0] == $expectedResult6)
            $this->stdout('| Test #6 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #6 failed                   |'."\n", Console::FG_RED);

        if ($testResult7[0] == $expectedResult7)
            $this->stdout('| Test #7 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #7 failed                   |'."\n", Console::FG_RED);

        if ($testResult8[0] == $expectedResult8)
            $this->stdout('| Test #8 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #8 failed                   |' . "\n", Console::FG_RED);

        $this->stdout(str_repeat("-", 36)."\n", Console::FG_PURPLE);

        return ExitCode::OK;
    }
    //--------------------------------------------------------------------------


    //--Экшн и вспомогательные функции тестирования учебных групп и обучающихся--
    public function actionGroupTest()
    {
        $this->stdout("\n\n| Training groups and group participants tests\n", Console::FG_CYAN);
        $this->GetGroup();
        $this->stdout("\n");
        $this->GetGroupParticipants();
        $this->stdout("\n");
        $this->GetVisits();
        $this->stdout("\n|".str_repeat("-", 45)."\n", Console::FG_CYAN);
    }

    private function GetGroup()
    {
        $preTestResult1 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-01-01', '2023-03-01');
        $preTestResult2 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2022-01-01', '2022-12-31');
        $preTestResult3 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2024-06-12', '2024-12-12');
        $preTestResult4 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-01-01', '2024-01-01');
        $preTestResult5 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-01-01', '2024-01-01', [BranchWork::CDNTT, BranchWork::TECHNO]);
        $preTestResult6 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-01-01', '2024-01-01', BranchWork::ALL, [FocusWork::TECHNICAL, FocusWork::ART]);
        $preTestResult7 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-01-01', '2024-01-01', BranchWork::ALL, FocusWork::ALL, [AllowRemoteWork::FULLTIME]);
        $preTestResult8 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-01-01', '2024-01-01', BranchWork::ALL, FocusWork::ALL, AllowRemoteWork::ALL, [ReportConst::BUDGET]);
        $preTestResult9 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-01-01', '2024-01-01', BranchWork::ALL, FocusWork::ALL, AllowRemoteWork::ALL, ReportConst::BUDGET_ALL, [1, 5]);
        $preTestResult10 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-01-01', '2023-12-11', [BranchWork::TECHNO, BranchWork::CDNTT, BranchWork::ADMIN], [FocusWork::TECHNICAL, FocusWork::SPORT], AllowRemoteWork::ALL, ReportConst::BUDGET_ALL, [4]);

        $testResult1 = [];
        foreach ($preTestResult1 as $one) $testResult1[] = $one->id;

        $testResult2 = [];
        foreach ($preTestResult2 as $one) $testResult2[] = $one->id;

        $testResult3 = [];
        foreach ($preTestResult3 as $one) $testResult3[] = $one->id;

        $testResult4 = [];
        foreach ($preTestResult4 as $one) $testResult4[] = $one->id;

        $testResult5 = [];
        foreach ($preTestResult5 as $one) $testResult5[] = $one->id;

        $testResult6 = [];
        foreach ($preTestResult6 as $one) $testResult6[] = $one->id;

        $testResult7 = [];
        foreach ($preTestResult7 as $one) $testResult7[] = $one->id;

        $testResult8 = [];
        foreach ($preTestResult8 as $one) $testResult8[] = $one->id;

        $testResult9 = [];
        foreach ($preTestResult9 as $one) $testResult9[] = $one->id;

        $testResult10 = [];
        foreach ($preTestResult10 as $one) $testResult10[] = $one->id;

        $expectedResult1 = [1, 2];
        $expectedResult2 = [];
        $expectedResult3 = [];
        $expectedResult4 = [1, 2, 3, 4, 5];
        $expectedResult5 = [1, 3, 4];
        $expectedResult6 = [2, 3, 4, 5];
        $expectedResult7 = [1, 2, 3, 5];
        $expectedResult8 = [1, 2, 3];
        $expectedResult9 = [1, 2, 3];
        $expectedResult10 = [4];


        $this->stdout("\n-----------(Groups tests)-----------\n|".str_repeat(" ", 34)."|\n", Console::FG_PURPLE);

        if ($testResult1 == $expectedResult1)
            $this->stdout('| Test #1 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #1 failed                   |'."\n", Console::FG_RED);

        if ($testResult2 == $expectedResult2)
            $this->stdout('| Test #2 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #2 failed                   |'."\n", Console::FG_RED);

        if ($testResult3 == $expectedResult3)
            $this->stdout('| Test #3 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #3 failed                   |'."\n", Console::FG_RED);

        if ($testResult4 == $expectedResult4)
            $this->stdout('| Test #4 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #4 failed                   |'."\n", Console::FG_RED);

        if ($testResult5 == $expectedResult5)
            $this->stdout('| Test #5 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #5 failed                   |'."\n", Console::FG_RED);

        if ($testResult6 == $expectedResult6)
            $this->stdout('| Test #6 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #6 failed                   |'."\n", Console::FG_RED);

        if ($testResult7 == $expectedResult7)
            $this->stdout('| Test #7 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #7 failed                   |'."\n", Console::FG_RED);

        if ($testResult8 == $expectedResult8)
            $this->stdout('| Test #8 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #8 failed                   |'."\n", Console::FG_RED);

        if ($testResult9 == $expectedResult9)
            $this->stdout('| Test #9 was passed successfully  |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #9 failed                   |'."\n", Console::FG_RED);

        if ($testResult10 == $expectedResult10)
            $this->stdout('| Test #10 was passed successfully |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #10 failed                  |'."\n", Console::FG_RED);

        $this->stdout(str_repeat("-", 36)."\n", Console::FG_PURPLE);
    }

    private function GetGroupParticipants()
    {
        $group1 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-01-01', '2023-03-01');
        $group2 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2022-01-01', '2022-12-31');
        $group4 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-01-01', '2024-01-01');
        $group8 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-01-01', '2024-01-01', BranchWork::ALL, FocusWork::ALL, AllowRemoteWork::ALL, [ReportConst::BUDGET]);

        $preTestResult1 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::TEST, $group1);
        $preTestResult2 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::TEST, $group2);
        $preTestResult3 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::TEST, $group4);
        $preTestResult4 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::TEST, $group4, 1);
        $preTestResult5 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::TEST, $group4, 1, [10, 11, 12, 13, 14, 15, 16, 17, 18], '2012-05-01');
        $preTestResult6 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::TEST, $group8, 0, [3, 4, 5, 6, 7, 8, 9], '2012-05-01');
        $preTestResult7 = SupportReportFunctions::GetDoubleParticipantsFromGroup(ReportConst::TEST, $group4);
        $preTestResult8 = SupportReportFunctions::GetCertificatsParticipantsFromGroup(ReportConst::TEST,
            SupportReportFunctions::GetParticipantsFromGroups(ReportConst::TEST, $group4));

        $testResult1 = [];
        foreach ($preTestResult1 as $one) $testResult1[] = $one->id;

        $testResult2 = [];
        foreach ($preTestResult2 as $one) $testResult2[] = $one->id;

        $testResult3 = [];
        foreach ($preTestResult3 as $one) $testResult3[] = $one->id;

        $testResult4 = [];
        foreach ($preTestResult4 as $one) $testResult4[] = $one->id;

        $testResult5 = [];
        foreach ($preTestResult5 as $one) $testResult5[] = $one->id;

        $testResult6 = [];
        foreach ($preTestResult6 as $one) $testResult6[] = $one->id;

        $testResult7 = [];
        foreach ($preTestResult7 as $one) $testResult7[] = $one->participant_id;

        $testResult8 = [];
        foreach ($preTestResult8 as $one) $testResult8[] = $one->training_group_participant_id;

        $expectedResult1 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $expectedResult2 = [];
        $expectedResult3 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30];
        $expectedResult4 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 13, 14, 15, 16, 17, 18, 24, 25, 26, 27];
        $expectedResult5 = [1, 2, 3, 6, 7, 8, 13, 14, 15, 18, 24];
        $expectedResult6 = [4, 5, 9, 10, 16, 17];
        $expectedResult7 = [1, 2, 7, 8, 9, 10, 11, 14, 18, 20];
        $expectedResult8 = [1, 2, 4, 6, 9, 12, 13, 14, 15, 19, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30];


        $this->stdout("\n--------(Participants tests)--------\n|" . str_repeat(" ", 34) . "|\n", Console::FG_PURPLE);

        if ($testResult1 == $expectedResult1)
            $this->stdout('| Test #1 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #1 failed                   |' . "\n", Console::FG_RED);

        if ($testResult2 == $expectedResult2)
            $this->stdout('| Test #2 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #2 failed                   |' . "\n", Console::FG_RED);

        if ($testResult3 == $expectedResult3)
            $this->stdout('| Test #3 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #3 failed                   |' . "\n", Console::FG_RED);

        if ($testResult4 == $expectedResult4)
            $this->stdout('| Test #4 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #4 failed                   |'."\n", Console::FG_RED);

        if ($testResult5 == $expectedResult5)
            $this->stdout('| Test #5 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #5 failed                   |'."\n", Console::FG_RED);

        if ($testResult6 == $expectedResult6)
            $this->stdout('| Test #6 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #6 failed                   |'."\n", Console::FG_RED);

        if ($testResult7 == $expectedResult7)
            $this->stdout('| Test #7 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #7 failed                   |'."\n", Console::FG_RED);

        if ($testResult8 == $expectedResult8)
            $this->stdout('| Test #8 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #8 failed                   |'."\n", Console::FG_RED);

        $this->stdout(str_repeat("-", 36)."\n", Console::FG_PURPLE);
    }

    private function GetVisits()
    {
        $group1 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-01-01', '2023-03-01');
        $group2 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-01-01', '2024-01-01');
        $group3 = SupportReportFunctions::GetTrainingGroups(ReportConst::TEST, '2023-03-01', '2024-01-01', [BranchWork::ADMIN]);

        $participants1 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::TEST, $group1);
        $participants2 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::TEST, $group2);
        $participants3 = SupportReportFunctions::GetParticipantsFromGroups(ReportConst::TEST, $group3);


        $testResult1 = SupportReportFunctions::GetVisits(ReportConst::TEST, $participants1, '2023-01-01', '2024-12-12', VisitWork::ALL);
        $testResult2 = SupportReportFunctions::GetVisits(ReportConst::TEST, $participants1, '2023-01-01', '2024-12-12', VisitWork::ONLY_PRESENCE);
        $testResult3 = SupportReportFunctions::GetVisits(ReportConst::TEST, $participants1, '2023-01-01', '2024-12-12', VisitWork::PRESENCE_AND_ABSENCE);
        $testResult4 = SupportReportFunctions::GetVisits(ReportConst::TEST, $participants2, '2023-01-01', '2024-12-12', VisitWork::ALL);
        $testResult5 = SupportReportFunctions::GetVisits(ReportConst::TEST, $participants2, '2023-01-01', '2024-12-12', VisitWork::ONLY_PRESENCE);
        $testResult6 = SupportReportFunctions::GetVisits(ReportConst::TEST, $participants2, '2023-01-01', '2024-12-12', VisitWork::PRESENCE_AND_ABSENCE);
        $testResult7 = SupportReportFunctions::GetVisits(ReportConst::TEST, $participants2, '2023-03-02', '2024-08-02', VisitWork::PRESENCE_AND_ABSENCE);
        $testResult8 = SupportReportFunctions::GetVisits(ReportConst::TEST, $participants2, '2023-10-05', '2024-08-02', VisitWork::PRESENCE_AND_ABSENCE);
        $testResult9 = SupportReportFunctions::GetVisits(ReportConst::TEST, $participants3, '2023-03-01', '2024-01-01', VisitWork::PRESENCE_AND_ABSENCE, [1]);

        $expectedResult1 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25,
            26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42];
        $expectedResult2 = [1, 2, 5, 6, 10, 11, 12, 13, 14, 17, 18, 19, 22, 23, 25, 26, 27, 28, 29, 30,
            31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 42];
        $expectedResult3 = [1, 2, 3, 4, 5, 6, 7, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 22, 23, 24, 25,
            26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42];

        $expectedResult4 = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26,
            27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54];
        $expectedResult5 = [1, 2, 5, 6, 10, 11, 12, 13, 14, 17, 18, 19, 22, 23, 25, 26, 27, 28, 29, 30,
            31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 42, 43, 45, 46, 48, 49, 51, 52, 53, 54];
        $expectedResult6 = [1, 2, 3, 4, 5, 6, 7, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 22, 23, 24, 25, 26, 27,
            28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54];

        $expectedResult7 = [6, 7, 13, 14, 27, 28, 34, 35, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54];
        $expectedResult8 = [43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54];
        $expectedResult9 = [43, 47, 51];


        $this->stdout("\n-----------(Visits tests)-----------\n|" . str_repeat(" ", 34) . "|\n", Console::FG_PURPLE);


        if ($testResult1 == $expectedResult1)
            $this->stdout('| Test #1 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #1 failed                   |'."\n", Console::FG_RED);

        if ($testResult2 == $expectedResult2)
            $this->stdout('| Test #2 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #2 failed                   |'."\n", Console::FG_RED);

        if ($testResult3 == $expectedResult3)
            $this->stdout('| Test #3 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #3 failed                   |'."\n", Console::FG_RED);

        if ($testResult4 == $expectedResult4)
            $this->stdout('| Test #4 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #4 failed                   |'."\n", Console::FG_RED);

        if ($testResult5 == $expectedResult5)
            $this->stdout('| Test #5 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #5 failed                   |'."\n", Console::FG_RED);

        if ($testResult6 == $expectedResult6)
            $this->stdout('| Test #6 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #6 failed                   |'."\n", Console::FG_RED);

        if ($testResult7 == $expectedResult7)
            $this->stdout('| Test #7 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #7 failed                   |'."\n", Console::FG_RED);

        if ($testResult8 == $expectedResult8)
            $this->stdout('| Test #8 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #8 failed                   |'."\n", Console::FG_RED);

        if ($testResult9 == $expectedResult9)
            $this->stdout('| Test #9 was passed successfully  |' . "\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #9 failed                   |'."\n", Console::FG_RED);

        $this->stdout(str_repeat("-", 36)."\n", Console::FG_PURPLE);
    }
    //---------------------------------------------------------------------------
}
