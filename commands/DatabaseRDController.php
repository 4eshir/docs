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
use tests\database_rd\RD_constants;
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
class DatabaseRDController extends Controller
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


    public function actionCheckIntegrity($displayMode = RD_constants::DISPLAY_ERROR)
    {
        $rdModel = new DatabaseRD();
        $rdModel->SetDbArray();

        $result = $rdModel->CheckDbIntegrity();

        $errorsTables = 0;
        $errorsColumns = 0;

        foreach ($result as $one)
        {
            //--Выбираем цвет вывода в зависимости от результата проверки--
            $color = Console::FG_GREEN;
            if (!$one->result[0])
            {
                $color = Console::FG_RED;
                $errorsTables++;
            }
            //--------------------------------------------------------------

            if ($color == Console::FG_GREEN && $displayMode == RD_constants::DISPLAY_ALL || $color == Console::FG_RED)
            {
                $this->stdout("\n".str_repeat('-', strlen($one->tablename) + 2)."\n", $color);
                $this->stdout('|'.$one->tablename."|\n", $color);
                $this->stdout(str_repeat('-', strlen($one->tablename) + 2)."\n", $color);
            }



            foreach ($one->result[1] as $key => $dTable)
            {

                $this->stdout('∟ '.$key."\n", $color);
                foreach ($dTable as $oCol)
                {
                    $errorsColumns++;
                    $this->stdout('  ∟ '.$oCol."\n", $color);
                }
            }



        }

        if ($errorsTables == 0)
            $this->stdout("\nПроблем не обнаружено\n", Console::FG_GREEN);
        else
        {
            $this->stdout("\nОбнаружены несоответствия в базе данных и файле '".$rdModel->filename."'\n",
                Console::FG_YELLOW);
            $this->stdout("Ошибок в таблицах: ".$errorsTables."\n", Console::FG_RED);
            $this->stdout("Ошибок в столбцах: ".$errorsColumns."\n", Console::FG_RED);
        }

    }


}
