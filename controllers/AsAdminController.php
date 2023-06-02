<?php

namespace app\controllers;

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
class AsAdminController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'add-company', 'add-country', 'add-license', 'delete-file', 'delete',
                            'add-as-type', 'index-company', 'index-country', 'index-license', 'index-as-type', 'delete-install', 'get-file',
                            'delete-file-commercial', 'delete-file-scan', 'delete-file-license', 'delete-as-type', 'delete-company', 'refresh-license',
                            'delete-country', 'delete-license'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all AsAdmin models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchAsAdmin();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AsAdmin model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AsAdmin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AsAdminWork();
        $modelUseYears = [new UseYearsWork()];
        $modelAsInstall = [new AsInstallWork()];

        if ($model->load(Yii::$app->request->post())) {
            $model->service_note = '';
            $model->commercial_offers = '';
            $model->scan = '';
            //$model->register_id = Yii::$app->user->identity->getId();

            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->licenseFile = UploadedFile::getInstance($model, 'licenseFile');
            $model->serviceNoteFile = UploadedFile::getInstances($model, 'serviceNoteFile');
            $model->commercialFiles = UploadedFile::getInstances($model, 'commercialFiles');
            //$modelUseYears = DynamicModel::createMultiple(UseYears::classname());
            //DynamicModel::loadMultiple($modelUseYears, Yii::$app->request->post());
            //$model->useYears = $modelUseYears;

            $modelAsInstall = DynamicModel::createMultiple(AsInstallWork::classname());
            DynamicModel::loadMultiple($modelAsInstall, Yii::$app->request->post());
            $model->asInstalls = $modelAsInstall;

            if ($model->validate(false)) {
                if ($model->scanFile !== null)
                    $model->uploadScanFile();
                if ($model->serviceNoteFile !== null)
                    $model->uploadServiceNoteFiles();
                if ($model->commercialFiles !== null)
                    $model->uploadCommercialFiles();
                if ($model->licenseFile !== null)
                    $model->uploadLicenseFile();
                $model->save(false);

            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelUseYears' => (empty($modelUseYears)) ? [new UseYearsWork] : $modelUseYears,
            'modelAsInstall' => (empty($modelAsInstall)) ? [new AsInstallWork] : $modelAsInstall,
        ]);
    }

    /**
     * Updates an existing AsAdmin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelAsInstall = [new AsInstallWork];
        $res = \app\models\common\UseYears::find()->where(['as_admin_id' => $model->id])->one();
        if ($res->start_date !== '1999-01-01') $model->useStartDate = $res->start_date;
        if ($res->end_date !== '1999-01-01') $model->useEndDate = $res->end_date;

        if ($model->load(Yii::$app->request->post())) {

            $modelAsInstall = DynamicModel::createMultiple(AsInstallWork::classname());
            DynamicModel::loadMultiple($modelAsInstall, Yii::$app->request->post());
            $model->asInstalls = $modelAsInstall;

            //$res = \app\models\common\UseYears::find()->where(['as_admin_id' => $model->id])->one();
            //if ($model->useStartDate !== "") $res->start_date = $model->useStartDate;
            //if ($model->useEndDate !== "") $res->end_date = $model->useEndDate;
            //$res->save(false);

            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->licenseFile = UploadedFile::getInstance($model, 'licenseFile');
            $model->serviceNoteFile = UploadedFile::getInstances($model, 'serviceNoteFile');
            $model->commercialFiles = UploadedFile::getInstances($model, 'commercialFiles');

            if ($model->validate(false))
            {
                if ($model->scanFile !== null)
                    $model->uploadScanFile();
                if ($model->serviceNoteFile !== null)
                    $model->uploadServiceNoteFiles();
                if ($model->commercialFiles !== null)
                    $model->uploadCommercialFiles();
                if ($model->licenseFile !== null)
                    $model->uploadLicenseFile();
                $model->save(false);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AsAdmin model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AsAdmin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AsAdminWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AsAdminWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetFile($fileName = null, $modelId = null)
    {

        $filePath = '/upload/files/'.Yii::$app->controller->id;

        $downloadServ = new FileDownloadServer($filePath, $fileName);
        $downloadYadi = new FileDownloadYandexDisk($filePath, $fileName);

        $downloadServ->LoadFile();
        if (!$downloadServ->success) $downloadYadi->LoadFile();
        else return \Yii::$app->response->sendFile($downloadServ->file);

        if (!$downloadYadi->success) throw new \Exception('File not found');
        else {

            $fp = fopen('php://output', 'r');

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $downloadYadi->filename);
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . $downloadYadi->file->size);

            $downloadYadi->file->download($fp);

            fseek($fp, 0);
        }
    }


    //--------------------------

    public function actionIndexCompany()
    {
        $model = AsCompanyWork::find()->all();
        return $this->render('index-company', ['model' => $model]);
    }

    public function actionAddCompany()
    {
        $model = new AsCompanyWork();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->addFlash('success', 'Компания успешно добавлена');
            return $this->redirect('index.php?r=as-admin/index-company');
        }

        return $this->render('add-company', ['model' => $model]);
    }

    public function actionDeleteCompany($model_id)
    {
        $model = AsCompanyWork::find()->where(['id' => $model_id])->one();
        if (count(AsAdminWork::find()->where(['as_company_id' => $model_id])->all()) == 0)
            $model->delete();
        else
            Yii::$app->session->addFlash('error', 'Невозможно удалить компанию! (используется в списке ПО)');
        return $this->redirect('index.php?r=as-admin/index-company');
    }

    //---------------------------------

    public function actionIndexCountry()
    {
        $model = CountryWork::find()->all();
        return $this->render('index-country', ['model' => $model]);
    }

    public function actionAddCountry()
    {
        $model = new CountryWork();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->addFlash('success', 'Страна успешно добавлена');
            return $this->redirect('index.php?r=as-admin/index-country');
        }

        return $this->render('add-country', ['model' => $model]);
    }

    public function actionDeleteCountry($model_id)
    {
        $model = CountryWork::find()->where(['id' => $model_id])->one();
        if (count(AsAdminWork::find()->where(['country_prod_id' => $model_id])->all()) == 0)
            $model->delete();
        else
            Yii::$app->session->addFlash('error', 'Невозможно удалить страну! (используется в списке ПО)');
        return $this->redirect('index.php?r=as-admin/index-country');
    }

    //---------------------------------

    public function actionIndexAsType()
    {
        $model = AsTypeWork::find()->all();
        return $this->render('index-as-type', ['model' => $model]);
    }

    public function actionAddAsType()
    {
        $model = new AsTypeWork();

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->addFlash('success', 'Тип успешно добавлен');
            return $this->redirect('index.php?r=as-admin/index-as-type');
        }

        return $this->render('add-as-type', ['model' => $model]);
    }

    public function actionDeleteAsType($model_id)
    {
        $model = AsTypeWork::find()->where(['id' => $model_id])->one();
        if (count(AsAdminWork::find()->where(['as_type_id' => $model_id])->all()) == 0)
            $model->delete();
        else
            Yii::$app->session->addFlash('error', 'Невозможно удалить тип! (используется в списке ПО)');
        return $this->redirect('index.php?r=as-admin/index-as-type');
    }

    //---------------------------------

    public function actionIndexLicense()
    {
        $model = LicenseWork::find()->all();
        return $this->render('index-license', ['model' => $model]);
    }

    public function actionAddLicense()
    {
        $model = new LicenseWork;

        if ($model->load(Yii::$app->request->post()) && $model->save())
        {
            Yii::$app->session->addFlash('success', 'Тип лицензии успешно добавлен');
            return $this->redirect('index.php?r=as-admin/index-license');
        }

        return $this->render('add-license', ['model' => $model]);
    }

    public function actionDeleteLicense($model_id)
    {
        $model = LicenseWork::find()->where(['id' => $model_id])->one();
        if (count(AsAdminWork::find()->where(['license_id' => $model_id])->all()) == 0)
            $model->delete();
        else
            Yii::$app->session->addFlash('error', 'Невозможно удалить тип лицензии! (используется в списке ПО)');
        return $this->redirect('index.php?r=as-admin/index-license');
    }

    //--------------------------------------------

    public function actionDeleteInstall($id, $model_id)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        $inst = AsInstallWork::find()->where(['id' => $id])->one();
        $inst->delete();
        $model = $this->findModel($model_id);
        return $this->redirect('index.php?r=as-admin/update&id='.$model->id);
    }

    public function actionDeleteFile($fileName = null, $modelId = null)
    {
        $model = AsAdminWork::find()->where(['id' => $modelId])->one();

        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null)
        {

            $result = '';
            $split = explode(" ", $model->service_note);
            for ($i = 0; $i < count($split) - 1; $i++)
            {
                if ($split[$i] !== $fileName)
                {
                    $result = $result.$split[$i].' ';
                }
            }
            $model->service_note = $result;
            $model->save(false);
        }
        return $this->redirect('index.php?r=as-admin/update&id='.$model->id);
    }

    public function actionDeleteFileScan($modelId = null)
    {
        $model = AsAdminWork::find()->where(['id' => $modelId])->one();
        $model->scan = '';
        $model->save(false);
        return $this->redirect('index.php?r=as-admin/update&id='.$model->id);
    }

    public function actionDeleteFileLicense($modelId = null)
    {
        $model = AsAdminWork::find()->where(['id' => $modelId])->one();
        $model->license_file = '';
        $model->save(false);
        return $this->redirect('index.php?r=as-admin/update&id='.$model->id);
    }

    public function actionDeleteFileCommercial($fileName = null, $modelId = null)
    {

        $model = AsAdminWork::find()->where(['id' => $modelId])->one();

        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null)
        {

            $result = '';
            $split = explode(" ", $model->commercial_offers);
            for ($i = 0; $i < count($split) - 1; $i++)
            {
                if ($split[$i] !== $fileName)
                {
                    $result = $result.$split[$i].' ';
                }
            }
            $model->commercial_offers = $result;
            $model->save(false);
        }
        return $this->redirect('index.php?r=as-admin/update&id='.$model->id);
    }

    public function actionRefreshLicense()
    {
        
        $as = AsAdminWork::find()->all();
        $date = new DateTime(date("Y-m-d"));
        foreach ($as as $asOne)
        {

            if (($asOne->getUseEndDate() > $date->format('Y-m-d') || $asOne->getUseEndDate() == '1999-01-01') && $asOne->getUseStartDate() < $date->format('Y-m-d') )
                $asOne->license_status = 1;
            else
                $asOne->license_status = 0;
            $asOne->save(false);
        }
        return $this->redirect('index.php?r=as-admin/index');
    }

    //Проверка на права доступа к CRUD-операциям
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (Yii::$app->user->identity->getId() !== 26){//!RoleBaseAccess::CheckAccess($action->controller->id, $action->id, Yii::$app->user->identity->getId())) {
            $this->redirect(['/site/error-access']);
            return false;
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}
