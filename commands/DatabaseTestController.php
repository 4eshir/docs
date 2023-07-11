<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\components\YandexDiskContext;
use app\models\LoginForm;
use app\models\strategies\FileDownloadStrategy\FileDownloadYandexDisk;
use app\models\work\PeopleWork;
use app\models\work\VisitWork;
use tests\database_rd\DatabaseRD;
use tests\other\DatabaseFileAccessTest;
use tests\other\models\FileAccessTest\FileAccessModel;
use Yii;
use yii\base\ErrorException;
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
class DatabaseTestController extends Controller
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

    public function actionIndex($message = 'hello world')
    {

        $this->stdout($message."\n", Console::FG_RED);
        //echo '<color="green">'.$message.'</color>' . "\n";

        return ExitCode::OK;
    }

    public function actionTemp()
    {
        $array = [
            new DatabaseRD(),
            't1' => ['1', '2', '3'],
            't2' => ['2', '2', '3'],
            't3' => ['3', '2', '3'],
            't4' => ['4', '2', '3'],
        ];


        $class = new PeopleWork();
        $query = null;
        $result = 0;

        $col = 'firstname';

        try {
            $query = $class::find()->where([$col => 1])->all();
        } catch (\yii\db\Exception $e) {
            $result = 1;
            Yii::warning("Division by zero1111.");
        }


        $this->stdout($result."\n", Console::FG_GREEN);




        //$res = DatabaseRD::SearchTableData($array, key($array[1]));
        //$this->stdout($res."\n", Console::FG_GREEN);

        return ExitCode::OK;
    }

    /**
     * Экшн для проверки доступности
     * файлов системы с сервера или Яндекс.Диска
     */
    public function actionCheckFileAccess()
    {
        /*$res = YandexDiskContext::CheckSameFile(FileDownloadYandexDisk::ADDITIONAL_PATH.'/upload/files/document-in/scan/Вх.20210111_1_Минобр_АО_О_внесении_изменений_в_состав_рабочей_группы_по_созданию_регионального_центра_Астриус.pdf') ? 'YES' : 'NO';
        $this->stdout($res."\n", Console::FG_GREEN);*/

        $serverCount = 0;
        $yadiCount = 0;
        $dropCount = 0;

        $dropFilepathes = [];

        $tester = new DatabaseFileAccessTest();
        $accesses = $tester->GetFileAccess([0]);

        foreach ($accesses as $one)
        {
            if ($one->access)
            {
                if ($one->repoType == FileAccessModel::SERV) $serverCount += 1;
                if ($one->repoType == FileAccessModel::YADI) $yadiCount += 1;
            }
            else
            {
                $dropCount += 1;
                $dropFilepathes[] = $one->filepath;

            }
        }


        $this->stdout("Files status\n", Console::FG_YELLOW);
        $this->stdout("On Server: ".$serverCount." || On Yandex: ".$yadiCount." || Dropped: ".$dropCount."\n\n", Console::FG_GREEN);

        $this->stdout("-------------\nDropped files\n-------------\n", Console::FG_YELLOW);
        foreach ($dropFilepathes as $path)
            $this->stdout($path."\n", Console::FG_RED);

        return ExitCode::OK;
    }


    public function actionCheckVisitIntegrity()
    {
        ini_set('memory_limit', '2048MB');
        set_time_limit(10000);

        $tester = new DatabaseFileAccessTest();
        $accesses = $tester->CheckVisitIntegrity();

        for ($i = 0; $i < count($accesses[0]); $i++)
            if ($accesses[1][$i] == 1) $this->stdout($accesses[0][$i]."\n", Console::FG_GREEN);
            else $this->stdout($accesses[0][$i]."\n", Console::FG_RED);

        $this->stdout("\n\n");

        $samesVis = $tester->CheckVisitSame();
        $this->stdout(count($samesVis));
        foreach ($samesVis as $vis)
            $this->stdout($vis->id."\n", Console::FG_RED);
    }

}
