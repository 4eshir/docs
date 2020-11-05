<?php

namespace app\controllers;

use app\models\common\AsInstall;
use app\models\common\Responsible;
use app\models\common\UseYears;
use app\models\DynamicModel;
use Yii;
use app\models\common\AsAdmin;
use app\models\SearchAsAdmin;
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
        $model = new AsAdmin();
        $modelUseYears = [new UseYears];
        $modelAsInstall = [new AsInstall];

        if ($model->load(Yii::$app->request->post())) {
            $model->service_note = '';
            $model->scan = '';
            $model->register_id = Yii::$app->user->identity->getId();

            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->serviceNoteFile = UploadedFile::getInstances($model, 'serviceNoteFile');

            $modelUseYears = DynamicModel::createMultiple(UseYears::classname());
            DynamicModel::loadMultiple($modelUseYears, Yii::$app->request->post());
            $model->useYears = $modelUseYears;

            $modelAsInstall = DynamicModel::createMultiple(AsInstall::classname());
            DynamicModel::loadMultiple($modelAsInstall, Yii::$app->request->post());
            $model->asInstalls = $modelAsInstall;

            if ($model->validate(false)) {
                $model->uploadScanFile();
                $model->uploadServiceNoteFiles();
                $model->save(false);

            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelUseYears' => (empty($modelUseYears)) ? [new UseYears] : $modelUseYears,
            'modelAsInstall' => (empty($modelAsInstall)) ? [new AsInstall] : $modelAsInstall,
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

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
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
     * @return AsAdmin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AsAdmin::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetFile($fileName = null, $modelId = null)
    {

        if ($fileName !== null && !Yii::$app->user->isGuest) {
            $currentFile = Yii::$app->basePath.'/upload/files/as_admin/'.$fileName;
            if (is_file($currentFile)) {
                header("Content-Type: application/octet-stream");
                header("Accept-Ranges: bytes");
                header("Content-Length: " . filesize($currentFile));
                header("Content-Disposition: attachment; filename=" . $fileName);
                readfile($currentFile);
                return $this->redirect('index.php?r=docs-out/create');
            };
        }
        //return $this->redirect('index.php?r=docs-out/index');
    }
}
