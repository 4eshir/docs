<?php

namespace app\controllers;

use app\models\common\Responsible;
use app\models\DynamicModel;
use Yii;
use app\models\common\DocumentOrder;
use app\models\SearchDocumentOrder;
use yii\helpers\ArrayHelper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * DocumentOrderController implements the CRUD actions for DocumentOrder model.
 */
class DocumentOrderController extends Controller
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
     * Lists all DocumentOrder models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchDocumentOrder();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DocumentOrder model.
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
     * Creates a new DocumentOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $model = new DocumentOrder();

        $modelResponsible = [new Responsible];
        if ($model->load(Yii::$app->request->post())) {
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->scan = 'init';

            $modelResponsible = DynamicModel::createMultiple(Responsible::classname());
            DynamicModel::loadMultiple($modelResponsible, Yii::$app->request->post());
            $model->responsibles = $modelResponsible;

            if ($model->validate(false)) {
                $path = '@app/upload/files/';
                $model->scan = $model->scanFile;
                $model->save();
                $model->scanFile->saveAs( $path . $model->scanFile);

            }
            return $this->redirect(['index']);
        }

        return $this->render('create', [
            'model' => $model,
            'modelResponsible' => (empty($modelResponsible)) ? [new Responsible] : $modelResponsible
        ]);
    }

    /**
     * Updates an existing DocumentOrder model.
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
     * Deletes an existing DocumentOrder model.
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
     * Finds the DocumentOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DocumentOrder the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentOrder::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
