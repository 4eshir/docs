<?php

namespace app\controllers;

use app\models\components\RoleBaseAccess;
use app\models\DynamicModel;
use app\models\work\AccessLevelWork;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\Password;
use app\models\work\AuthorProgramWork;
use app\models\work\RoleFunctionRoleWork;
use app\models\work\RoleWork;
use app\models\work\UserRoleWork;
use Yii;
use app\models\work\UserWork;
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
        $model = $this->findModel($id);
        /*
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 1])->one() !== null) $model->addUsers = 1; else $model->addUsers = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 2])->one() !== null) $model->viewRoles = 1; else $model->viewRoles = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 3])->one() !== null) $model->editRoles = 1; else $model->editRoles = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 4])->one() !== null) $model->viewOut = 1; else $model->viewOut = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 5])->one() !== null) $model->editOut = 1; else $model->editOut = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 6])->one() !== null) $model->viewIn = 1; else $model->viewIn = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 7])->one() !== null) $model->editIn = 1; else $model->editIn = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 8])->one() !== null) $model->viewOrder = 1; else $model->viewOrder = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 9])->one() !== null) $model->editOrder = 1; else $model->editOrder = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 10])->one() !== null) $model->viewRegulation = 1; else $model->viewRegulation = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 11])->one() !== null) $model->editRegulation = 1; else $model->editRegulation = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 12])->one() !== null) $model->viewEvent = 1; else $model->viewEvent = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 13])->one() !== null) $model->editEvent = 1; else $model->editEvent = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 14])->one() !== null) $model->viewAS = 1; else $model->viewAS = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 15])->one() !== null) $model->editAS = 1; else $model->editAS = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 16])->one() !== null) $model->viewAdd = 1; else $model->viewAdd = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 17])->one() !== null) $model->editAdd = 1; else $model->editAdd = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 18])->one() !== null) $model->viewForeign = 1; else $model->viewForeign = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 19])->one() !== null) $model->editForeign = 1; else $model->editForeign = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 20])->one() !== null) $model->viewProgram = 1; else $model->viewProgram = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 21])->one() !== null) $model->editProgram = 1; else $model->editProgram = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 22])->one() !== null) $model->viewGroup = 1; else $model->viewGroup = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 23])->one() !== null) $model->editGroup = 1; else $model->editGroup = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 24])->one() !== null) $model->viewGroupBranch = 1; else $model->viewGroupBranch = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 25])->one() !== null) $model->editGroupBranch = 1; else $model->editGroupBranch = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 26])->one() !== null) $model->addGroup = 1; else $model->addGroup = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 27])->one() !== null) $model->deleteGroup = 1; else $model->deleteGroup = 0;
        if (AccessLevelWork::find()->where(['user_id' => $id])->andWhere(['access_id' => 28])->one() !== null) $model->report = 1; else $model->report = 0;


        */
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
        $model = new UserWork();
        $modelRole = [new RoleWork];

        if ($model->load(Yii::$app->request->post())) {
            $model->setPassword($model->password_hash);
            $model->generateAuthKey();
            $model->creator_id = Yii::$app->user->identity->getId();
            $model->save();
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлен новый пользователь '.$model->username);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelRole' => $modelRole,
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
        $model = $this->findModel($id);
        $modelRole = [new RoleWork];

        if ($model->load(Yii::$app->request->post())) {
            $modelRole = DynamicModel::createMultiple(RoleWork::classname());
            DynamicModel::loadMultiple($modelRole, Yii::$app->request->post());
            $model->roles = $modelRole;
            //$model->last_update_id = Yii::$app->user->identity->getId();
            $model->save();
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменен пользователь '.$model->username);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelRole' => $modelRole,
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
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален пользователь '.$this->findModel($id)->username);
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    public function actionDeleteRole($roleId, $modelId)
    {
        $role = UserRoleWork::find()->where(['id' => $roleId])->one();
        $name = $role->role->name;
        $role->delete();
        $user = UserWork::find()->where(['id' => $modelId])->one();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Откреплена роль ' . $name . ' от пользователя '. $user->secondname . ' ' . $user->firstname);

        return $this->redirect('index?r=user/update&id='.$modelId);
    }


    /**
     * Finds the User model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return UserWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = UserWork::findOne($id)) !== null) {
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
