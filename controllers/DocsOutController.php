<?php

namespace app\controllers;

use app\models\components\RoleBaseAccess;
use app\models\strategies\FileDownloadStrategy\FileDownloadServer;
use app\models\strategies\FileDownloadStrategy\FileDownloadYandexDisk;
use app\models\work\CompanyWork;
use app\models\work\InOutDocsWork;
use app\models\work\PeoplePositionBranchWork;
use app\models\work\PeopleWork;
use app\models\work\PositionWork;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use Yii;
use app\models\work\DocumentOutWork;
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
    public function actionIndex($archive = null, $type = null)
    {
        $session = Yii::$app->session;
        if ($archive !== null && $type !== null)
            $session->set("archiveOut", "1");
        if ($archive === null && $type !== null)
            $session->remove("archiveOut");

        $searchModel = new SearchDocumentOut($archive);
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
        $model = new DocumentOutWork();
        $model->document_name = "default";

        if($model->load(Yii::$app->request->post()))
        {
            $model->applications = '';
            $model->doc = '';
            $model->getDocumentNumber();
            $model->Scan = '';

            $model->creator_id = Yii::$app->user->identity->getId();
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
        $model = new DocumentOutWork();

        $model->document_theme = 'Резерв';
        $model->document_name = '';
        //$model->document_date = end(DocumentOutWork::find()->orderBy(['document_number' => SORT_ASC, 'document_postfix' => SORT_ASC])->all())->document_date;
        $model->document_date = date("Y-m-d");
        $model->sent_date = '1999-01-01';
        $model->Scan = '';
        $model->applications = '';
        $model->creator_id = Yii::$app->user->identity->getId();
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
        $model = $this->findModel($id);

        $model->scanFile = $model->Scan;
        $inoutdocs = InOutDocsWork::find()->where(['document_out_id' => $model->id])->one();
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
        
        $name = $this->findModel($id)->document_theme;
        $theme = $this->findModel($id)->document_theme;
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален исходящий документ '.$theme);
        $this->findModel($id)->delete();
        Yii::$app->session->addFlash('success', 'Документ "'.$name.'" успешно удален');

        return $this->redirect(['index']);
    }



    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {

        $model = DocumentOutWork::find()->where(['id' => $modelId])->one();

        if ($type == 'scan')
        {
            $model->Scan = '';
            $model->save(false);
            return $this->redirect('index?r=docs-out/update&id='.$modelId);
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
        return $this->redirect('index?r=docs-out/update&id='.$modelId);
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

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $downloadYadi->filename);
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . $downloadYadi->file->size);

            $downloadYadi->file->download($fp);

            exit;

        }
    }

    public function actionPositions()
    {
        $parents = Yii::$app->request->post('depdrop_parents', null);
        if ($parents != null) {
            $positions = $parents[0];
            $arr = PositionWork::find()->where(['id' => $positions->position_id])->one();
            return Json::encode(array(
                'output' => $arr,
                'selected' => $positions
            ));
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
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

    /**
     * Finds the DocumentOut model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DocumentOutWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentOutWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //Проверка на права доступа к CRUD-операциям
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!RoleBaseAccess::CheckAccess($action->controller->id, $action->id, Yii::$app->user->identity->getId())) {
            $this->redirect(['/site/error-access']);
            return false;
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}
