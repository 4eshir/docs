<?php

namespace app\controllers;

use app\models\common\AccessLevel;
use app\models\components\UserRBAC;
use Yii;
use app\models\common\User;
use app\models\UserSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UserController implements the CRUD actions for User model.
 */
class UserController extends Controller
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
     * Lists all User models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id))
            return $this->render('/site/error');
        $searchModel = new UserSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id))
            return $this->render('/site/error');

        $model = $this->findModel($id);
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 1])->one() !== null) $model->addUsers = 1; else $model->addUsers = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 2])->one() !== null) $model->viewRoles = 1; else $model->viewRoles = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 3])->one() !== null) $model->editRoles = 1; else $model->editRoles = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 4])->one() !== null) $model->viewOut = 1; else $model->viewOut = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 5])->one() !== null) $model->editOut = 1; else $model->editOut = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 6])->one() !== null) $model->viewIn = 1; else $model->viewIn = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 7])->one() !== null) $model->editIn = 1; else $model->editIn = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 8])->one() !== null) $model->viewOrder = 1; else $model->viewOrder = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 9])->one() !== null) $model->editOrder = 1; else $model->editOrder = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 10])->one() !== null) $model->viewRegulation = 1; else $model->viewRegulation = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 11])->one() !== null) $model->editRegulation = 1; else $model->editRegulation = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 12])->one() !== null) $model->viewEvent = 1; else $model->viewEvent = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 13])->one() !== null) $model->editEvent = 1; else $model->editEvent = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 14])->one() !== null) $model->viewAS = 1; else $model->viewAS = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 15])->one() !== null) $model->editAS = 1; else $model->editAS = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 16])->one() !== null) $model->viewAdd = 1; else $model->viewAdd = 0;
        if (AccessLevel::find()->where(['user_id' => $id])->andWhere(['access_id' => 17])->one() !== null) $model->editAdd = 1; else $model->editAdd = 0;
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new User model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id))
            return $this->render('/site/error');
        $model = new User();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing User model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id))
            return $this->render('/site/error');
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing User model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id))
            return $this->render('/site/error');
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return User the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
