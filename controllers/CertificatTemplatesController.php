<?php

namespace app\controllers;

use app\models\components\RoleBaseAccess;
use app\models\strategies\FileDownloadStrategy\FileDownloadServer;
use app\models\strategies\FileDownloadStrategy\FileDownloadYandexDisk;
use Yii;
use app\models\common\CertificatTemplates;
use app\models\SearchCertificatTemplates;
use app\models\components\Logger;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\work\CertificatTemplatesWork;
use yii\web\UploadedFile;

/**
 * CertificatTemplatesController implements the CRUD actions for CertificatTemplates model.
 */
class CertificatTemplatesController extends Controller
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
     * Lists all CertificatTemplates models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchCertificatTemplates();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CertificatTemplates model.
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
     * Creates a new CertificatTemplates model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CertificatTemplatesWork();

        if ($model->load(Yii::$app->request->post()))
        {
            $model->templateFile = UploadedFile::getInstance($model, 'templateFile');
            //$model->path =
            if ($model->templateFile !== null)
            {
                $model->uploadTemplateFile();
                $model->save(false);
                return $this->redirect(['view', 'id' => $model->id]);
            }

        }

        Yii::$app->session->setFlash('danger', 'Невозможно добавить шаблон без подложки');
        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CertificatTemplates model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            $model->templateFile = UploadedFile::getInstance($model, 'templateFile');
            if ($model->templateFile !== null)
                $model->uploadTemplateFile();
            
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CertificatTemplates model.
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
     * Finds the CertificatTemplates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CertificatTemplates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CertificatTemplatesWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Страница не найдена');
    }

    public function actionGetFile($fileName = null, $modelId = null)
    {
        $filePath = '/upload/files/'.Yii::$app->controller->id.'/';

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

    /*public function actionDeleteFile($modelId = null)
    {
        $model = CertificatTemplatesWork::find()->where(['id' => $modelId])->one();

        if (!Yii::$app->user->isGuest && $modelId !== null)
        {
            $deleteFile = $model->path;
            $model->path = null;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален файл ' . $deleteFile);
        }
        return $this->redirect('index?r=certificat_templates/update&id='.$model->id);
    }*/
    //Проверка на права доступа к CRUD-операциям
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!RoleBaseAccess::CheckAccess($action->controller->id, $action->id, Yii::$app->user->identity->getId())) {
            return $this->redirect(['/site/error-access']);
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}
