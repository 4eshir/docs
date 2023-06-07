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
        $res = file_exists('sftp://u1471742@37.140.192.82/var/www/u1471742/data/www/index.schooltech.ru/docs/upload/files/training-program/edit_docs/%D0%A0%D0%B5%D0%B41_20230515_%D0%9E%D0%BB%D0%B8%D0%BC%D0%BF%D0%B8%D0%B0%D0%B4%D0%BD%D0%B0%D1%8F_%D1%84%D0%B8%D0%B7%D0%B8%D0%BA%D0%B0_%D0%B2_%D1%8D%D0%BA%D1%81%D0%BF%D0%B5%D1%80%D0%B8%D0%BC%D0%B5%D0%BD%D1%82%D0%B0%D0%BB%D1%8C%D0%BD%D1%8B%D1%85_%D0%B7%D0%B0%D0%B4%D0%B0%D1%87%D0%B0%D1%85._%D0%92%D0%B2%D0%BE%D0%B4%D0%BD%D1%8B%D0%B9_%D1%83%D1%80%D0%BE%D0%B2%D0%B5%D0%BD%D1%8C.docx') ? '+' : '-';
        $this->stdout($res, Console::FG_GREEN);

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
