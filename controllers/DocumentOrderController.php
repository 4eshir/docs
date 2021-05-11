<?php

namespace app\controllers;

use app\models\common\Expire;
use app\models\common\Regulation;
use app\models\common\Responsible;
use app\models\components\Logger;
use app\models\components\UserRBAC;
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
    public function actionIndex($sort = null)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
        $searchModel = new SearchDocumentOrder();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sort);

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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
        $model = new DocumentOrder();
        $model->order_number = "02-02";
        $modelExpire = [new Expire];
        $modelResponsible = [new Responsible];
        if ($model->load(Yii::$app->request->post())) {
            $model->signed_id = null;
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
            $model->scan = '';
            $model->state = true;

            $modelResponsible = DynamicModel::createMultiple(Responsible::classname());
            DynamicModel::loadMultiple($modelResponsible, Yii::$app->request->post());
            $model->responsibles = $modelResponsible;
            $modelExpire = DynamicModel::createMultiple(Expire::classname());
            DynamicModel::loadMultiple($modelExpire, Yii::$app->request->post());
            $model->expires = $modelExpire;

            if ($model->validate(false)) {
                $model->getDocumentNumber();
                if ($model->scanFile !== null)
                    $model->uploadScanFile();
                if ($model->docFiles != null)
                    $model->uploadDocFiles();
                
                $model->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменен приказ '.$model->order_name);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelResponsible' => (empty($modelResponsible)) ? [new Responsible] : $modelResponsible,
            'modelExpire' => (empty($modelExpire)) ? [new Expire] : $modelExpire,
        ]);
    }

    public function actionCreateReserve()
    {
        $model = new DocumentOrder();

        $model->order_name = 'Резерв';
        $model->order_number = '02-02';
        $model->order_date = end(DocumentOrder::find()->orderBy(['order_copy_id' => SORT_ASC, 'order_postfix' => SORT_ASC])->all())->order_date;
        $model->scan = '';
        $model->state = true;
        $model->register_id = Yii::$app->user->identity->getId();
        $model->getDocumentNumber();
        Yii::$app->session->addFlash('success', 'Резерв успешно добавлен');
        $model->save(false);
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлен резерв приказа '.$model->order_number.'/'.$model->order_postfix);
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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
        $model = $this->findModel($id);
        $modelResponsible = DynamicModel::createMultiple(Responsible::classname());
        $modelExpire = DynamicModel::createMultiple(Expire::classname());
        DynamicModel::loadMultiple($modelResponsible, Yii::$app->request->post());
        $model->responsibles = $modelResponsible;
        if ($model->load(Yii::$app->request->post())) {
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
            $modelResponsible = DynamicModel::createMultiple(Responsible::classname());
            DynamicModel::loadMultiple($modelResponsible, Yii::$app->request->post());
            $model->responsibles = $modelResponsible;
            DynamicModel::loadMultiple($modelExpire, Yii::$app->request->post());
            $model->expires = $modelExpire;


            if ($model->validate(false)) {
                if ($model->scanFile !== null)
                    $model->uploadScanFile();
                if ($model->docFiles != null)
                    $model->uploadDocFiles(10);

                $model->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменен приказ '.$model->order_name);
            }

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelResponsible' => (empty($modelResponsible)) ? [new Responsible] : $modelResponsible,
            'modelExpire' => (empty($modelExpire)) ? [new Expire] : $modelExpire,
        ]);
    }

    public function actionDeleteExpire($expireId, $modelId)
    {
        $expire = Expire::find()->where(['id' => $expireId])->one();
        $order = DocumentOrder::find()->where(['id' => $expire->expire_order_id])->one();
        if ($order !== null)
        {
            $order->state = 1;
            Regulation::CheckRegulationState($order->id, 1);
            $order->save(false);
            $model = DocumentOrder::find()->where(['id' => $modelId])->one();

        }
        $reg = Regulation::find()->where(['id' => $expire->expire_regulation_id])->one();
        if ($reg !== null)
        {
            $reg->state = 'Утратило силу';
            $reg->save(false);
        }
        $expire->delete();

        $model = DocumentOrder::find()->where(['id' => $modelId])->one();
        return $this->render('update', [
            'model' => $model,
            'modelResponsible' => (empty($modelResponsible)) ? [new Responsible] : $modelResponsible,
            'modelExpire' => (empty($modelExpire)) ? [new Expire] : $modelExpire
        ]);
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {

        $model = DocumentOrder::find()->where(['id' => $modelId])->one();

        if ($type == 'scan')
        {
            $model->scan = '';
            $model->save(false);
            return $this->redirect('index?r=document-order/update&id='.$model->id);
        }


        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null) {

            $result = '';
            $split = explode(" ", $model->doc);
            $deleteFile = '';
            for ($i = 0; $i < count($split) - 1; $i++) {
                if ($split[$i] !== $fileName) {
                    $result = $result . $split[$i] . ' ';
                } else
                    $deleteFile = $split[$i];
            }
            $model->doc = $result;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален файл ' . $deleteFile);
        }
        return $this->redirect('index?r=document-order/update&id='.$model->id);
    }

    public function actionGetFile($fileName = null, $modelId = null, $type = null)
    {
        $file = Yii::$app->basePath . '/upload/files/order/' . $type . '/' . $fileName;
        if (file_exists($file)) {
            return \Yii::$app->response->sendFile($file);
        }
        throw new \Exception('File not found');
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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
        $order = $this->findModel($id);
        $name = $order->order_name;
        if (!$order->checkForeignKeys())
        {
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален приказ '.$name);
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
