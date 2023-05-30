<?php

namespace app\controllers;

use app\models\components\RoleBaseAccess;
use app\models\strategies\FileDownloadStrategy\FileDownloadServer;
use app\models\strategies\FileDownloadStrategy\FileDownloadYandexDisk;
use app\models\work\ExpireWork;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\DynamicModel;
use Yii;
use app\models\work\RegulationWork;
use app\models\SearchRegulation;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * RegulationController implements the CRUD actions for Regulation model.
 */
class RegulationController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Regulation models.
     * @return mixed
     */
    public function actionIndex($c = null)
    {
        $session = Yii::$app->session;
        $session->set('type', $c);
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        $searchModel = new SearchRegulation();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $c);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Regulation model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Regulation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new RegulationWork();
        $modelExpire = [new ExpireWork];
        if ($model->load(Yii::$app->request->post())) {
            $session = Yii::$app->session;
            $model->regulation_type_id = $session->get('type');
            $model->state = 1;
            $modelExpire = DynamicModel::createMultiple(ExpireWork::classname());
            DynamicModel::loadMultiple($modelExpire, Yii::$app->request->post());
            $model->expires = $modelExpire;

            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->scan = '';

            if ($model->validate(false))
            {
                if ($model->scanFile !== null)
                    $model->uploadScanFile();
                $model->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлено положение '.$model->name);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelExpire' => (empty($modelExpire)) ? [new ExpireWork] : $modelExpire
        ]);
    }

    /**
     * Updates an existing Regulation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelExpire = [new ExpireWork];
        if ($model->load(Yii::$app->request->post())) {
            RegulationWork::CheckRegulationState($model->order_id);
            $modelExpire = DynamicModel::createMultiple(ExpireWork::classname());
            DynamicModel::loadMultiple($modelExpire, Yii::$app->request->post());
            $model->expires = $modelExpire;
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->scan = '';
            if ($model->validate(false))
            {
                if ($model->scanFile !== null)
                    $model->uploadScanFile();
                $model->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменено положение '.$model->name);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelExpire' => (empty($modelExpire)) ? [new ExpireWork] : $modelExpire
        ]);
    }

    /**
     * Deletes an existing Regulation model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $reg = $this->findModel($id);

        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удалено положение '.$reg->name);
        $reg->delete();

        return $this->redirect(['index']);
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

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {
        $model = RegulationWork::find()->where(['id' => $modelId])->one();
        if ($type == 'scan')
        {
            $model->scan = '';
            $model->save(false);
        }
        return $this->render('update', [
            'model' => $this->findModel($modelId),
        ]);
    }


    /**
     * Finds the Regulation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return RegulationWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = RegulationWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //Проверка на права доступа к CRUD-операциям
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        $session = Yii::$app->session;
        $c = $_GET['c'];
        if ($_GET['c']  === null) $c = $session->get('type');
        if (!RoleBaseAccess::CheckAccess($action->controller->id, $action->id, Yii::$app->user->identity->getId(), $c == '1' ? 1 : 2)) {
            $this->redirect(['/site/error-access']);
            return false;
        }
        return parent::beforeAction($action);
    }
}
