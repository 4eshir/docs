<?php

namespace app\controllers;

use app\models\common\DocumentOut;
use app\models\common\InOutDocs;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use Yii;
use app\models\common\DocumentIn;
use app\models\SearchDocumentIn;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * DocumentInController implements the CRUD actions for DocumentIn model.
 */
class DocumentInController extends Controller
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
     * Lists all DocumentIn models.
     * @return mixed
     */
    public function actionIndex($sort = null)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id))
            return $this->render('/site/error');
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        $searchModel = new SearchDocumentIn();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sort);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DocumentIn model.
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
     * Creates a new DocumentIn model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id))
            return $this->render('/site/error');
        $model = new DocumentIn();


        if ($model->load(Yii::$app->request->post())) {
            $model->local_number = 0;
            $model->signed_id = null;
            $model->target = null;
            $model->get_id = null;
            $model->applications = '';
            $model->scan = '';
            if ($model->correspondent_id !== null)
            {
                $model->company_id = $model->correspondent->company_id;
                $model->position_id = $model->correspondent->position_id;
            }

            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->applicationFiles = UploadedFile::getInstances($model, 'applicationFiles');
            $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
            if ($model->validate(false))
            {
                $model->getDocumentNumber();
                if ($model->scanFile != null)
                    $model->uploadScanFile();
                if ($model->applicationFiles != null)
                    $model->uploadApplicationFiles();
                if ($model->docFiles != null)
                    $model->uploadDocFiles();

                $model->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлен входящий документ '.$model->document_theme);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionCreateReserve()
    {

        $model = new DocumentIn();

        $model->document_theme = 'Резерв';

        $model->local_date = end(DocumentIn::find()->orderBy(['local_number' => SORT_ASC, 'local_postfix' => SORT_ASC])->all())->local_date;
        $model->real_date = '1999-01-01';
        $model->scan = '';
        $model->applications = '';
        $model->register_id = Yii::$app->user->identity->getId();
        $model->getDocumentNumber();
        Yii::$app->session->addFlash('success', 'Резерв успешно добавлен');
        $model->save(false);
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлен резерв входящего документа '.$model->local_number.'/'.$model->local_postfix);
        return $this->redirect('index.php?r=document-in/index');
    }

    /**
     * Updates an existing DocumentIn model.
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

        $model->scanFile = $model->scan;

        $links = InOutDocs::find()->where(['document_in_id' => $model->id])->one();
        if ($links !== null)
            $model->needAnswer = 1;

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
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменен входящий документ '.$model->document_theme);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DocumentIn model.
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
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален входящий документ '.$name);
        $this->findModel($id)->delete();
        Yii::$app->session->addFlash('success', 'Документ "'.$name.'" успешно удален');

        return $this->redirect(['index']);
    }

    /**
     * Finds the DocumentIn model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DocumentIn the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentIn::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionGetFile($fileName = null, $modelId = null, $type = null)
    {
        $file = Yii::$app->basePath . '/upload/files/document_in/' . $type . '/' . $fileName;
        if (file_exists($file)) {
            return \Yii::$app->response->sendFile($file);
        }
        throw new \Exception('File not found');
        //return $this->redirect('index.php?r=docs-out/index');
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {

        $model = DocumentIn::find()->where(['id' => $modelId])->one();

        if ($type == 'scan')
        {
            $model->scan = '';
            $model->save(false);
            return $this->redirect('index?r=document-in/update&id='.$modelId);
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
            return $this->redirect('index?r=document-in/update&id='.$modelId);
        }
        return $this->redirect('index.php?r=document-in/update&id='.$modelId);
    }
}
