<?php

namespace app\controllers;

use app\models\common\InOutDocs;
use app\models\common\Position;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use Yii;
use app\models\common\DocumentOut;
use app\models\SearchDocumentOut;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use \kartik\depdrop\DepDrop;

/**
 * DocsOutController implements the CRUD actions for DocumentOut model.
 */
class DocsOutController extends Controller
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
                        'actions' => [],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
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
     * Lists all DocumentOut models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }

        $searchModel = new SearchDocumentOut();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DocumentOut model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id))
            return $this->render('/site/error');
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DocumentOut model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id))
            return $this->render('/site/error');
        $model = new DocumentOut();
        $model->document_name = "default";

        if($model->load(Yii::$app->request->post()))
        {
            $model->applications = '';
            $model->doc = '';
            $model->getDocumentNumber();
            $model->Scan = '';

            $model->register_id = Yii::$app->user->identity->getId();
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->applicationFiles = UploadedFile::getInstances($model, 'applicationFiles');
            $model->docFiles = UploadedFile::getInstances($model, 'docFiles');


            if ($model->validate(false)) {
                if ($model->scanFile != null)
                    $model->uploadScanFile();
                if ($model->applicationFiles != null)
                    $model->uploadApplicationFiles();
                if ($model->docFiles != null)
                    $model->uploadDocFiles();
                $model->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлен исходящий документ '.$model->document_theme);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }




        return $this->render('/docs-out/create', [
            'model' => $model,
        ]);
    }

    public function actionCreateReserve()
    {
        $model = new DocumentOut();

        $model->document_theme = 'Резерв';
        $model->document_name = '';
        $model->document_date = end(DocumentOut::find()->orderBy(['document_number' => SORT_ASC, 'document_postfix' => SORT_ASC])->all())->document_date;
        $model->sent_date = '1999-01-01';
        $model->Scan = '';
        $model->applications = '';
        $model->register_id = Yii::$app->user->identity->getId();
        $model->getDocumentNumber();
        Yii::$app->session->addFlash('success', 'Резерв успешно добавлен');
        $model->save(false);
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Создан резерв исходящего документа '.$model->document_number.'/'.$model->document_postfix);
        return $this->redirect('index.php?r=docs-out/index');
    }

    /**
     * Updates an existing DocumentOut model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id))
            return $this->render('/site/error');
        $model = $this->findModel($id);
        $model->scanFile = $model->Scan;
        $inoutdocs = InOutDocs::find()->where(['document_out_id' => $model->id])->one();
        if ($inoutdocs !== null)
            $model->isAnswer = $inoutdocs->id;

        if($model->load(Yii::$app->request->post()))
        {

            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->applicationFiles = UploadedFile::getInstances($model, 'applicationFiles');
            $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
            if ($model->validate(false)) {
                if ($model->scanFile != null)
                    $model->uploadScanFile();
                if ($model->applicationFiles != null)
                    $model->uploadApplicationFiles(10);
                if ($model->docFiles != null)
                    $model->uploadDocFiles(10);
                $model->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменен исходящий документ '.$model->document_theme);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DocumentOut model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id))
            return $this->render('/site/error');
        $name = $this->findModel($id)->document_theme;
        $theme = $this->findModel($id)->document_theme;
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален исходящий документ '.$theme);
        $this->findModel($id)->delete();
        Yii::$app->session->addFlash('success', 'Документ "'.$name.'" успешно удален');

        return $this->redirect(['index']);
    }

    /**
     * Finds the DocumentOut model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DocumentOut the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentOut::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {

        $model = DocumentOut::find()->where(['id' => $modelId])->one();

        if ($type == 'scan')
        {
            $model->Scan = '';
            $model->save(false);
            return $this->render('update', [
                'model' => $this->findModel($modelId),
            ]);
        }

        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null)
        {

            $result = '';
            $type == 'app' ? $split = explode(" ", $model->applications) : $split = explode(" ", $model->doc);
            $deleteFile = '';
            for ($i = 0; $i < count($split) - 1; $i++)
            {
                if ($split[$i] !== $fileName)
                {
                    $result = $result.$split[$i].' ';
                }
                else
                    $deleteFile = $split[$i];
            }
            $type == 'app' ? $model->applications = $result : $model->doc = $result;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален файл '.$deleteFile);
        }
        return $this->render('update', [
            'model' => $this->findModel($modelId),
        ]);
    }

    public function actionGetFile($fileName = null, $modelId = null, $type = null)
    {
        $file = Yii::$app->basePath . '/upload/files/document_out/' . $type . '/' . $fileName;
        if (file_exists($file)) {
            return \Yii::$app->response->sendFile($file);
        }
        throw new \Exception('File not found');
        //return $this->redirect('index.php?r=docs-out/index');
    }

    public function actionPositions()
    {
        $parents = Yii::$app->request->post('depdrop_parents', null);
        if ($parents != null) {
            $positions = $parents[0];
            $arr = Position::find()->where(['id' => $positions->position_id])->one();
            return Json::encode(array(
                'output' => $arr,
                'selected' => $positions
            ));
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }

}
