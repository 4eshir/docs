<?php

namespace app\controllers\external;

use app\models\strategies\FileDownloadStrategy\FileDownloadServer;
use app\models\strategies\FileDownloadStrategy\FileDownloadYandexDisk;
use app\models\work\AsInstallWork;
use app\models\work\AsTypeWork;
use app\models\work\CompanyWork;
use app\models\work\AsCompanyWork;
use app\models\work\CountryWork;
use app\models\work\VersionWork;
use app\models\work\LicenseWork;
use app\models\work\ResponsibleWork;
use app\models\work\UseYearsWork;
use app\models\components\UserRBAC;
use app\models\DynamicModel;
use DateTime;
use Yii;
use app\models\work\AsAdminWork;
use app\models\SearchAsAdmin;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * AsAdminController implements the CRUD actions for AsAdmin model.
 */
class BackupController extends Controller
{
    public function actionBackupDatabase()
    {
        // шаблон ошибки, выводимой в файл
        $error_template = 'mysqldump: Got error:';

        // путь сохранения файла бэкапа
        $filepath = Yii::$app->basePath.'/../db_backups/'.date('Ymd-his').'__db_dskd.sql';

        // конфигурации БД
        $db_config = include Yii::$app->basePath.'/config/db.php';

        $username = $db_config["username"];
        $password = $db_config["password"];
        $host = explode('=', explode(':', explode(';', $db_config["dsn"])[0])[1])[1];
        $db_name = explode('=', explode(';', $db_config["dsn"])[1])[1];

        // функция записи бэкапа в файл, с перенаправлением STDERR>STDOUT
        exec('mysqldump --user=' . $username . ' --password=' . $password . ' --host=' . $host .
            ' ' . $db_name . '> ' . $filepath . ' 2>&1');

        // получаем данные из файла
        $filedata = fopen($filepath, 'r') or die("Cannot find file!");
        $file_first_str = htmlentities(fgets($filedata));

        // если в файле информация об ошибке - удаляем файл
        if (var_export(stripos($file_first_str, $error_template), true) !== 'false')
            unlink($filepath);
    }

    public function actionBackupFiles()
    {
        //
    }

    private function scan($dir)
    {
        $dirCanonical = realpath($dir);
        if ($fileOrDir = opendir($dirCanonical))
        {
            while ($fileName = readdir($fileOrDir))
            {
                if($fileName == "." || $fileName == "..")
                    continue;

                $callBack=$dirCanonical.DIRECTORY_SEPARATOR.$fileName;
                echo $callBack,"<br>";
                if(is_dir($callBack)){
                    $this->scan($callBack);
                }
            }
        }
    }

}
