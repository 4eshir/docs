<?php

namespace app\controllers;

use app\models\components\RoleBaseAccess;
use app\models\work\CompanyWork;
use app\models\work\DocumentOutWork;
use app\models\work\InOutDocsWork;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\work\PeoplePositionBranchWork;
use app\models\work\PeopleWork;
use app\models\work\PositionWork;
use Arhitector\Yandex\Disk;
use Yii;
use app\models\work\DocumentInWork;
use app\models\SearchDocumentIn;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

use app\models\strategies\FileDownloadStrategy\FileDownloadServer;
use app\models\strategies\FileDownloadStrategy\FileDownloadYandexDisk;

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
    public function actionIndex($sort = null, $archive = null, $type = null)
    {
        $session = Yii::$app->session;
        if ($archive !== null && $type !== null)
            $session->set("archiveIn", "1");
        if ($archive === null && $type !== null)
            $session->remove("archiveIn");

        $searchModel = new SearchDocumentIn($archive);
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
        $model = new DocumentInWork();


        if ($model->load(Yii::$app->request->post())) {
            $model->creator_id = Yii::$app->user->identity->getId();
            $model->local_number = 0;
            $model->signed_id = null;
            $model->target = null;
            $model->get_id = null;
            $model->applications = '';
            $model->scan = '';
            if ($model->correspondent_id !== null)
            {
                //$model->company_id = $model->correspondent->company_id;
                //$model->position_id = $model->correspondent->position_id;
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

        $model = new DocumentInWork();

        $model->document_theme = 'Резерв';

        //$model->local_date = end(DocumentInWork::find()->orderBy(['local_number' => SORT_ASC, 'local_postfix' => SORT_ASC])->all())->local_date;
        $model->local_date = date("Y-m-d");
        $model->real_date = '1999-01-01';
        $model->scan = '';
        $model->applications = '';
        $model->creator_id = Yii::$app->user->identity->getId();
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
        $model = $this->findModel($id);

        $model->scanFile = $model->scan;

        $links = InOutDocsWork::find()->where(['document_in_id' => $model->id])->one();
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
     * @return DocumentInWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentInWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }


    public function actionGetFile($fileName = null, $modelId = null, $type = null)
    {
        $filePath = '/upload/files/'.Yii::$app->controller->id;
        $filePath .= $type == null ? '/' : '/'.$type.'/';

        $downloadServ = new FileDownloadServer($filePath, $fileName);
        $downloadYadi = new FileDownloadYandexDisk($filePath, $fileName);

        $downloadServ->LoadFile();
        if (!$downloadServ->success) $downloadYadi->LoadFile();
        else return \Yii::$app->response->sendFile($downloadServ->file);

        if (!$downloadYadi->success) throw new \Exception('File not found');
        else
        {

            $fp = fopen('php://output', 'r');

            //header('Content-Description: File Transfer');
            //header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $downloadYadi->filename);
            //header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . $downloadYadi->file->size);

            $downloadYadi->file->download($fp);

            fseek($fp, 0);

        }


        /*
        $file = Yii::$app->basePath . '/upload/files/document_in/' . $type . '/' . $fileName;
        if (file_exists($file)) {
            return \Yii::$app->response->sendFile($file);
        }
        throw new \Exception('File not found');
        */

        //return $this->redirect('index.php?r=docs-out/index');
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {

        $model = DocumentInWork::find()->where(['id' => $modelId])->one();

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

    public function actionSubcat()
    {

        if (Yii::$app->request->post('id') === "")
        {
            $operations = PositionWork::find()
                ->orderBy(['name' => SORT_ASC])
                ->all();
            foreach ($operations as $operation)
                echo "<option value='" . $operation->id . "'>" . $operation->name . "</option>";
            echo "|split|";
            $operations = CompanyWork::find()
                ->orderBy(['name' => SORT_ASC])
                ->all();
            foreach ($operations as $operation)
                echo "<option value='" . $operation->id . "'>" . $operation->name . "</option>";
        }
        else
        {
            if ($id = Yii::$app->request->post('id')) {
                Yii::trace('$id=' . $id, 'значение id=');
                $operationPosts = PeoplePositionBranchWork::find()
                    ->where(['people_id' => $id])
                    ->count();

                if ($operationPosts > 0) {
                    $operations = PeoplePositionBranchWork::find()
                        ->where(['people_id' => $id])
                        ->all();
                    foreach ($operations as $operation)
                        echo "<option value='" . $operation->position_id . "'>" . $operation->position->name . "</option>";
                } else
                    echo "<option>-</option>";

                echo "|split|";
                $people = PeopleWork::find()->where(['id' => $id])->one();
                $operationPosts = CompanyWork::find()
                    ->where(['id' => $people->company_id])
                    ->count();

                if ($operationPosts > 0) {
                    $operations = CompanyWork::find()
                        ->where(['id' => $people->company_id])
                        ->all();
                    foreach ($operations as $operation)
                        echo "<option value='" . $operation->id . "'>" . $operation->name . "</option>";
                } else
                    echo "<option>-</option>";
            }
        }
    }

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
