<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands\report_test;

use app\commands\SupCommandsController;
use app\models\common\ForeignEventParticipants;
use app\models\components\report\ReportConst;
use app\models\components\report\SupportReportFunctions;
use app\models\LoginForm;
use app\models\work\BranchWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\ForeignEventWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\VisitWork;
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
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionBaseTest()
    {
        $this->stdout("\n-------Base tests (count: 2)-------\n|".str_repeat(" ", 33)."|\n", Console::FG_PURPLE);

        $testResult1 = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2020-01-01', '2023-12-31', 0);
        $testResult2 = SupportReportFunctions::GetParticipants(ReportConst::TEST, '2020-01-01', '2023-12-31', 1);

        if (count($testResult1[0]) == 0)
            $this->stdout('| Test #1 was passed successfully |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #1 failed                  |'."\n", Console::FG_RED);


        return ExitCode::OK;
    }

    public function actionBranchTest()
    {
        $this->stdout("\n-------Branch tests (count: 2)-------\n|".str_repeat(" ", 35)."|\n", Console::FG_PURPLE);

        $data = include Yii::$app->basePath.'\tests\_data\report\get-participants.php';
        $data = $data[1];

        $testResult1 = SupportReportFunctions::GetParticipants($data, '2020-01-01', '2023-12-31', 0, ReportConst::EVENT_LEVELS, [BranchWork::TECHNO]);
        $testResult2 = SupportReportFunctions::GetParticipants($data, '2020-01-01', '2023-12-31', 1, ReportConst::EVENT_LEVELS, [BranchWork::TECHNO]);

        $this->stdout((string)($testResult1[0] .' || '. $data['result'][0])."\n");
        $this->stdout((string)($testResult1[1] .' || '. $data['result'][1])."\n");
        $this->stdout((string)($testResult1[2] .' || '. $data['result'][2])."\n");

        for ($i = 0; $i < count($testResult1[0]); $i++)
        {
            $this->stdout($data['result'][0][$i].' '.$data['result'][0][$i]."\n");
        }

        if ($testResult1[0] == $data['result'][0] &&
            $testResult1[1] == $data['result'][1] &&
            $testResult1[2] == $data['result'][2])
            $this->stdout('| Test #1 was passed successfully   |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #1 failed                    |'."\n", Console::FG_RED);



        if ($testResult2[0] == $data['result'][0] &&
            $testResult2[1] == $data['result'][1] &&
            $testResult2[2] == $data['result'][2])
            $this->stdout('| Test #2 was passed successfully   |'."\n", Console::FG_GREEN);
        else
            $this->stdout('| Test #2 failed                    |'."\n", Console::FG_RED);

        $this->stdout("--------------------------------------\n", Console::FG_PURPLE);

        return ExitCode::OK;
    }
}
