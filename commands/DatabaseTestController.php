<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\LoginForm;
use app\models\work\VisitWork;
use tests\other\DatabaseFileAccessTest;
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

    /**
     * Экшн для проверки доступности
     * файлов системы с сервера или Яндекс.Диска
     */
    public function actionCheckFileAccess()
    {
        $res = file_exists('/var/www/u1471742/data/www/index.schooltech.ru/docs//upload/files/training-program/edit_docs/Ред1_20200116_Практическое_введение_в_профессию_журналиста_в_проектах_газеты_Мы_Можем.pdf') ? '+' : '-';
        $this->stdout($res."\n", Console::FG_GREEN);

        /*
        $tester = new DatabaseFileAccessTest();
        $accesses = $tester->GetFileAccess();

        foreach ($accesses as $one)
        {
            if ($one->access)
                $this->stdout("+".$one->filepath."\n", Console::FG_GREEN);
            else
                $this->stdout("-".$one->filepath."\n", Console::FG_RED);

        }
        */
        return ExitCode::OK;
    }

}
