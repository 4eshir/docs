<?php

namespace app\controllers;

use app\models\components\RoleBaseAccess;
use app\models\components\UserRBAC;
use app\models\strategies\FileDownloadStrategy\FileDownloadServer;
use app\models\strategies\FileDownloadStrategy\FileDownloadYandexDisk;
use app\models\work\AuditoriumWork;
use app\models\work\BranchWork;
use app\models\work\LegacyResponsibleWork;
use app\models\components\Logger;
use Yii;
use app\models\work\LocalResponsibilityWork;
use app\models\SearchLocalResponsibility;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * LocalResponsibilityController implements the CRUD actions for LocalResponsibility model.
 */
class LocalResponsibilityController extends Controller
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
     * Lists all LocalResponsibility models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchLocalResponsibility();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LocalResponsibility model.
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
     * Creates a new LocalResponsibility model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LocalResponsibilityWork();

        if ($model->load(Yii::$app->request->post())) {
            $model->filesStr = UploadedFile::getInstances($model, 'filesStr');
            if ($model->filesStr !== null)
                $model->uploadFiles();
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LocalResponsibility model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $subModel = LegacyResponsibleWork::find()->where(['people_id' => $model->people_id])->andWhere(['responsibility_type_id' => $model->responsibility_type_id])
            ->andWhere(['branch_id' => $model->branch_id])->andWhere(['auditorium_id' => $model->auditorium_id])->one();

        if ($subModel !== null)
        {
            $model->start_date = $subModel->start_date;
            $model->order_id = $subModel->order_id;
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->filesStr = UploadedFile::getInstances($model, 'filesStr');
            if ($model->end_date !== "")
                $model->detachResponsibility();
            if ($model->filesStr !== null)
                $model->uploadFiles(10);
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LocalResponsibility model.
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

    public function actionDeleteFile($fileName = null, $modelId = null)
    {

        $model = LocalResponsibilityWork::find()->where(['id' => $modelId])->one();

        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null) {

            $result = '';
            $split = explode(" ", $model->files);
            $deleteFile = '';
            for ($i = 0; $i < count($split) - 1; $i++) {
                if ($split[$i] !== $fileName) {
                    $result = $result . $split[$i] . ' ';
                } else
                    $deleteFile = $split[$i];
            }
            $model->files = $result;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален файл ' . $deleteFile);
        }
        return $this->redirect('index?r=local-responsibility/update&id='.$model->id);
    }

    public function actionSubcat()
    {
        if ($id = Yii::$app->request->post('id')) {
            $operationPosts = BranchWork::find()
                ->where(['id' => $id])
                ->count();

            if ($operationPosts > 0) {
                $operations = AuditoriumWork::find()
                    ->where(['branch_id' => $id])
                    ->all();
                echo "<option value=0>--</option>";
                foreach ($operations as $operation)
                    echo "<option value='" . $operation->id . "'>" . $operation->name . "</option>";
            } else
                echo "<option>-</option>";

        }
    }

    /**
     * Finds the LocalResponsibility model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LocalResponsibilityWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LocalResponsibilityWork::findOne($id)) !== null) {
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
            return $this->redirect(['/site/error-access']);
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}
