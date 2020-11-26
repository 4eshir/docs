<?php

namespace app\controllers;

use app\models\common\Expire;
use app\models\DynamicModel;
use Yii;
use app\models\common\Regulation;
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
    public function actionIndex()
    {
        $searchModel = new SearchRegulation();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

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
        $model = new Regulation();
        $modelExpire = [new Expire];
        if ($model->load(Yii::$app->request->post())) {
            $model->state = 'Актуально';
            $modelExpire = DynamicModel::createMultiple(Expire::classname());
            DynamicModel::loadMultiple($modelExpire, Yii::$app->request->post());
            $model->expires = $modelExpire;

            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->scan = '';

            if ($model->validate(false))
            {
                if ($model->scanFile !== null)
                    $model->uploadScanFile();
                $model->save(false);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelExpire' => (empty($modelExpire)) ? [new Expire] : $modelExpire
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
        $modelExpire = [new Expire];
        if ($model->load(Yii::$app->request->post())) {
            $modelExpire = DynamicModel::createMultiple(Expire::classname());
            DynamicModel::loadMultiple($modelExpire, Yii::$app->request->post());
            $model->expires = $modelExpire;

            if ($model->validate(false))
            {
                $model->save(false);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelExpire' => (empty($modelExpire)) ? [new Expire] : $modelExpire
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionGetFile($fileName = null, $modelId = null)
    {

        if ($fileName !== null && !Yii::$app->user->isGuest) {
            $currentFile = Yii::$app->basePath.'/upload/files/regulation/'.$fileName;
            if (is_file($currentFile)) {
                header("Content-Type: application/octet-stream");
                header("Accept-Ranges: bytes");
                header("Content-Length: " . filesize($currentFile));
                header("Content-Disposition: attachment; filename=" . $fileName);
                readfile($currentFile);
                return $this->redirect('index.php?r=regulation/create');
            };
        }
        //return $this->redirect('index.php?r=docs-out/index');
    }

    /**
     * Finds the Regulation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Regulation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Regulation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
