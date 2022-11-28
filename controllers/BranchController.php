<?php

namespace app\controllers;

use app\models\components\Logger;
use app\models\components\RoleBaseAccess;
use app\models\components\UserRBAC;
use app\models\work\AuditoriumWork;
use app\models\DynamicModel;
use Yii;
use app\models\work\BranchWork;
use app\models\SearchBranch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * BranchController implements the CRUD actions for Branch model.
 */
class BranchController extends Controller
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
     * Lists all Branch models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchBranch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Branch model.
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
     * Creates a new Branch model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new BranchWork();
        $modelAuditorium = [new AuditoriumWork];

        if ($model->load(Yii::$app->request->post())) {
            $modelAuditorium = DynamicModel::createMultiple(AuditoriumWork::classname());
            DynamicModel::loadMultiple($modelAuditorium, Yii::$app->request->post());
            $model->auditoriums = $modelAuditorium;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлен отдел '.$model->name);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelAuditorium' => $modelAuditorium,
        ]);
    }

    /**
     * Updates an existing Branch model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelAuditorium = [new AuditoriumWork];

        if ($model->load(Yii::$app->request->post())) {
            $modelAuditorium = DynamicModel::createMultiple(AuditoriumWork::classname());
            DynamicModel::loadMultiple($modelAuditorium, Yii::$app->request->post());
            $model->auditoriums = $modelAuditorium;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменен отдел '.$model->name);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelAuditorium' => $modelAuditorium,
        ]);
    }

    /**
     * Deletes an existing Branch model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $name = $model->name;
        $model->delete();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален отдел '.$name);

        return $this->redirect(['index']);
    }

    /**
     * Finds the Branch model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return BranchWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = BranchWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDeleteAuditorium($id, $modelId)
    {
        $participant = AuditoriumWork::find()->where(['id' => $id])->one();
        $participant->delete();
        return $this->redirect('index?r=branch/update&id='.$modelId);
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
