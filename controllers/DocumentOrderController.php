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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
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
        $model->order_number = "02-02";

        $modelResponsible = [new Responsible];
        if ($model->load(Yii::$app->request->post())) {
            $model->signed_id = null;
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->scan = '';

            $modelResponsible = DynamicModel::createMultiple(Responsible::classname());
            DynamicModel::loadMultiple($modelResponsible, Yii::$app->request->post());
            $model->responsibles = $modelResponsible;
            if ($model->validate(false)) {
                $model->getDocumentNumber();
                if ($model->scanFile !== null)
                    $model->uploadScanFile();
                
                $model->save(false);

            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelResponsible' => (empty($modelResponsible)) ? [new Responsible] : $modelResponsible
        ]);
    }

    public function actionCreateReserve()
    {
        $model = new DocumentOrder();

        $model->order_name = 'Резерв';
        $model->order_number = '02-02';
        $model->order_date = end(DocumentOrder::find()->orderBy(['order_copy_id' => SORT_ASC, 'order_postfix' => SORT_ASC])->all())->order_date;
        $model->scan = '';
        $model->register_id = Yii::$app->user->identity->getId();
        $model->getDocumentNumber();
        Yii::$app->session->addFlash('success', 'Резерв успешно добавлен');
        $model->save(false);

        return $this->redirect('index.php?r=document-order/index');
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
        $modelResponsible = DynamicModel::createMultiple(Responsible::classname());
        DynamicModel::loadMultiple($modelResponsible, Yii::$app->request->post());
        $model->responsibles = $modelResponsible;
        if ($model->load(Yii::$app->request->post())) {
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $modelResponsible = DynamicModel::createMultiple(Responsible::classname());
            DynamicModel::loadMultiple($modelResponsible, Yii::$app->request->post());
            $model->responsibles = $modelResponsible;


            if ($model->validate(false)) {
                if ($model->scanFile !== null)
                    $model->uploadScanFile();

                $model->save(false);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelResponsible' => (empty($modelResponsible)) ? [new Responsible] : $modelResponsible
        ]);
    }

    public function actionGetFile($fileName = null, $modelId = null)
    {

        if ($fileName !== null && !Yii::$app->user->isGuest) {
            $currentFile = Yii::$app->basePath.'/upload/files/order/'.$fileName;
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

    public function actionDeleteResponsible($peopleId, $orderId)
    {
        $resp = Responsible::find()->where(['people_id' => $peopleId])->andWhere(['document_order_id' => $orderId])->one();
        if ($resp != null)
            $resp->delete();
        $model = $this->findModel($orderId);
        return $this->redirect('index.php?r=document-order/update&id='.$orderId);
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
        $order = $this->findModel($id);
        $name = $order->order_name;
        if (!$order->checkForeignKeys())
        {
            $order->delete();
            Yii::$app->session->addFlash('success', 'Приказ "' . $name . '" успешно удален');
        }
        else
            Yii::$app->session->addFlash('error', 'Приказ "' . $name . '" невозможно удалить. Он упоминается в одном или нескольких положениях!');
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
