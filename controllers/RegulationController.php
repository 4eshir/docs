<?php

namespace app\controllers;

use app\models\common\Expire;
use app\models\components\Logger;
use app\models\components\UserRBAC;
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
    public function actionIndex($c = null)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
        $session = Yii::$app->session;
        $session->set('type', $c);
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        $searchModel = new SearchRegulation();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $c);

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
     * Creates a new Regulation model.
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
        $model = new Regulation();
        $modelExpire = [new Expire];
        if ($model->load(Yii::$app->request->post())) {
            $session = Yii::$app->session;
            $model->regulation_type_id = $session->get('type');
            $model->state = 1;
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
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлено положение '.$model->name);
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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
        $model = $this->findModel($id);
        $modelExpire = [new Expire];
        if ($model->load(Yii::$app->request->post())) {
            Regulation::CheckRegulationState($model->order_id);
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
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменено положение '.$model->name);
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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
        $reg = $this->findModel($id);

        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удалено положение '.$reg->name);
        $reg->delete();

        return $this->redirect(['index']);
    }

    public function actionGetFile($fileName = null, $modelId = null)
    {
        $file = Yii::$app->basePath . '/upload/files/regulation/' . $fileName;
        if (file_exists($file)) {
            return \Yii::$app->response->sendFile($file);
        }
        throw new \Exception('File not found');
        //return $this->redirect('index.php?r=docs-out/index');
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {
        $model = Regulation::find()->where(['id' => $modelId])->one();
        if ($type == 'scan')
        {
            $model->scan = '';
            $model->save(false);
        }
        return $this->render('update', [
            'model' => $this->findModel($modelId),
        ]);
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
