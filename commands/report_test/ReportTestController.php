<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands\report_test;

use app\commands\SupCommandsController;
use app\models\common\EventLevel;
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

    //--Экшн и вспомогательные функции для тестирования участников мероприятий--
    public function actionParticipantTest()
    {
        $this->GetParticipantsTest(); //Тест на выгрузку участников деятельности по заданным параметрам
        $this->stdout("\n");
        $this->ParticipantAchievementsTest(); //Тест на выгрузку победителей и призеров по заданным параметрам
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

        $expectedResult1 = [[2, 2, 3, 5, 6, 7, 8], [], 7];
        $expectedResult2 = [[2, 2, 3, 5, 6, 7, 8], [1, 2], 9];
        $expectedResult3 = [[2, 3, 5, 6, 7, 8], [], 6];
        $expectedResult4 = [[2, 3, 5, 6, 7, 8], [1, 2], 8];
        $expectedResult5 = [[2, 3], [1], 3];
        $expectedResult6 = [[2, 3], [1], 3];
        $expectedResult7 = [[], [1], 1];
        $expectedResult8 = [[6, 7, 8], [], 3];
        $expectedResult9 = [[2, 3, 5], [], 3];
        $expectedResult10 = [[], [], 0];

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
            $this->stdout('| Test #8 failed                   |' . "\n", Console::FG_RED);

        $this->stdout(str_repeat("-", 36)."\n", Console::FG_PURPLE);

        return ExitCode::OK;
    }
    //--------------------------------------------------------------------------
}
