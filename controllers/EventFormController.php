<?php

namespace app\controllers;

use app\models\components\UserRBAC;
use Yii;
use app\models\common\EventForm;
use app\models\SearchEventForm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EventFormController implements the CRUD actions for EventForm model.
 */
class EventFormController extends Controller
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
     * Lists all EventForm models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, 'Add')) {
            return $this->render('/site/error');
        }
        $searchModel = new SearchEventForm();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EventForm model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, 'Add')) {
            return $this->render('/site/error');
        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EventForm model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, 'Add')) {
            return $this->render('/site/error');
        }
        $model = new EventForm();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EventForm model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, 'Add')) {
            return $this->render('/site/error');
        }
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EventForm model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, 'Add')) {
            return $this->render('/site/error');
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EventForm model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EventForm the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EventForm::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
