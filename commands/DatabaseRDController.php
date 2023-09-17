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
use Symfony\Component\Console\Command\Command;
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

    //--Вывод записей, которые связаны с текущей записью в таблице--
    /*
     * $tablename - имя таблицы
     * $id - идентификатор записи в таблице
     */
    public function actionTableLinks($tablename = null, $id = -1)
    {
        $rdModel = new DatabaseRD();
        $rdModel->SetDbArray();

        $result = $rdModel->GetTableLinks($tablename, $id);

        foreach ($result as $one)
        {
            if (!$one->EmptyCheckColumnLinks())
            {
                $this->stdout("\n".str_repeat('-', strlen($one->tableName) + 2), Console::FG_GREEN);
                $this->stdout("\n|".$one->tableName."|\n", Console::FG_GREEN);
                $this->stdout(str_repeat('-', strlen($one->tableName) + 2)."\n", Console::FG_GREEN);

                foreach ($one->columnLinks as $col)
                {
                    if (!$col->EmptyCheckRows())
                    {
                        $this->stdout($col->columnName."\n", Console::FG_YELLOW);
                        $this->stdout(str_repeat('-', strlen($col->columnName))."\n", Console::FG_YELLOW);
                        foreach ($col->rows as $row)
                            $this->stdout($row." ", Console::FG_PURPLE);
                        $this->stdout("\n", Console::FG_PURPLE);
                        $this->stdout(str_repeat('-', strlen($col->columnName))."\n\n", Console::FG_YELLOW);
                    }

                }
            }

        }


        return ExitCode::OK;
    }
    //--------------------------------------------------------------

    //--Вызов рекурсивного удаления записи из БД--
    public function actionRecDelete($tablename, $id)
    {
        $rdModel = new DatabaseRD();
        $rdModel->SetDbArray();

        $result = $rdModel->GetTableLinks($tablename, $id);
        $sum = 0;
        foreach ($result as $table)
            $sum += count($table->GetAllRowsId());

        $this->stdout($sum."\n", Console::FG_GREEN);

        $rdModel->RecursiveDelete($tablename, $id);
        $this->stdout(count($rdModel->deleteItems)."\n\n", Console::FG_GREEN);

        for ($i = 0; $i < count($rdModel->deleteItems); $i++)
        {
            $endKey = $rdModel->deleteItems[$i]->endSign ? ' (END)' : '';
            $this->stdout($rdModel->deleteItems[$i]->tableName.": ".$rdModel->deleteItems[$i]->id.$endKey."\n", Console::FG_PURPLE);
        }

    }
    //--------------------------------------------

    //--Вывод общей информации о таблице с обратными зависимостями--
    public function actionTableInfo($tablename = null)
    {
        if ($tablename == null)
        {
            $this->stdout("\nНе задано имя таблицы\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $rdModel = new DatabaseRD();
        $rdModel->SetDbArray();

        $table = $rdModel->dbArray[$tablename];

        if ($table === null)
        {
            $this->stdout("\nТаблица с именем '$tablename' не найдена\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $this->stdout("\n#########################################################\n\nИмя таблицы: ".$tablename."\n\n\n-------------\n|Зависимости|\n-------------\n\n\n", Console::FG_GREEN);

        foreach ($table as $key => $row)
        {
            if (gettype($row) == 'array')
            {
                $this->stdout('Таблица: '.$key."\n", Console::FG_PURPLE);
                $this->stdout('---------'.str_repeat('-', strlen($key))."\n", Console::FG_PURPLE);
                $fieldStr = '';

                foreach ($row as $field)
                    $fieldStr .= '|'.$field.'|';
                $this->stdout($fieldStr."\n\n\n", Console::FG_YELLOW);
            }

        }

        $this->stdout("#########################################################\n\n", Console::FG_GREEN);

        return ExitCode::OK;
    }
    //--------------------------------------------------------------

    //--Проверка всех таблиц на целостность (соответствие полей в обратных зависимостях реальным полям в БД--
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

        return ExitCode::OK;
    }
    //-------------------------------------------------------------------------------------------------------


}
