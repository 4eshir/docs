<?php

namespace app\controllers;

use app\models\components\RoleBaseAccess;
use app\models\work\PeoplePositionBranchWork;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\DynamicModel;
use Yii;
use app\models\work\PeopleWork;
use app\models\SearchPeople;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PeopleController implements the CRUD actions for People model.
 */
class PeopleController extends Controller
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
     * Lists all People models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchPeople();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single People model.
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
     * Creates a new People model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PeopleWork();
        $modelPeoplePositionBranch = [new PeoplePositionBranchWork];

        if ($model->load(Yii::$app->request->post())) {
            $model->firstname = str_replace(' ', '', $model->firstname);
            $model->secondname = str_replace(' ', '', $model->secondname);
            $model->patronymic = str_replace(' ', '', $model->patronymic);
            $modelPeoplePositionBranch = DynamicModel::createMultiple(PeoplePositionBranchWork::classname());
            DynamicModel::loadMultiple($modelPeoplePositionBranch, Yii::$app->request->post());
            $model->positions = $modelPeoplePositionBranch;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлен новый человек '.$model->fullName);
            Yii::$app->session->addFlash('success', $model->secondname.' '.$model->firstname.' '.$model->patronymic.' ('.$model->position->name.') успешно добавлен');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelPeoplePositionBranch' => $modelPeoplePositionBranch,
        ]);
    }

    /**
     * Updates an existing People model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelPeoplePositionBranch = [new PeoplePositionBranchWork()];
        if ($model->position_id !== null)
            $model->stringPosition = $model->position->name;
        if ($model->load(Yii::$app->request->post())) {
            $modelPeoplePositionBranch = DynamicModel::createMultiple(PeoplePositionBranchWork::classname());
            DynamicModel::loadMultiple($modelPeoplePositionBranch, Yii::$app->request->post());
            $model->positions = $modelPeoplePositionBranch;
            $model->save();
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменен человек '.$model->fullName);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelPeoplePositionBranch' => $modelPeoplePositionBranch,
        ]);
    }

    /**
     * Deletes an existing People model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->checkForeignKeys())
        {
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален человек '.$model->fullName);
            $model->delete();
            Yii::$app->session->addFlash('success', $model->secondname.' '.$model->firstname.' '.$model->patronymic.' ('.$model->position->name.') успешно удален');
        }


        return $this->redirect(['index']);
    }

    public function actionDeletePosition($id, $modelId)
    {
        $position = PeoplePositionBranchWork::find()->where(['id' => $id])->one();
        $position->delete();
        return $this->redirect('index?r=people/update&id='.$modelId);
    }

    /**
     * Finds the People model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PeopleWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PeopleWork::findOne($id)) !== null) {
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
