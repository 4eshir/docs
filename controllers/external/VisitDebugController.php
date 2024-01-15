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
class VisitDebugController extends Controller
{
    // количество строк для бэкапа из таблицы visits
    const ROW_COUNT = 500*1000;

    public function actionPartialCopyVisits($partCount = 1000)
    {
        
    }

}
