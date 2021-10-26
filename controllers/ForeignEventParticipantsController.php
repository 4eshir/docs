<?php

namespace app\controllers;

use app\models\components\RoleBaseAccess;
use app\models\components\UserRBAC;
use app\models\extended\LoadParticipants;
use app\models\work\PersonalDataForeignEventParticipantWork;
use Yii;
use app\models\work\ForeignEventParticipantsWork;
use app\models\SearchForeignEventParticipants;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ForeignEventParticipantsController implements the CRUD actions for ForeignEventParticipants model.
 */
class ForeignEventParticipantsController extends Controller
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
     * Lists all ForeignEventParticipants models.
     * @return mixed
     */
    public function actionIndex($sort = null)
    {
        $searchModel = new SearchForeignEventParticipants();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $sort);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ForeignEventParticipants model.
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
     * Creates a new ForeignEventParticipants model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ForeignEventParticipantsWork();

        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model->checkOther();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ForeignEventParticipants model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $pdDatabase = PersonalDataForeignEventParticipantWork::find()->where(['foreign_event_participant_id' => $id])->all();
        if ($pdDatabase !== null)
        {
            $pdIds = [];
            foreach ($pdDatabase as $one)
                if ($one->status === 1)
                    $pdIds[] = $one->personal_data_id;
        }
        $model->pd = $pdIds;
        if ($model->load(Yii::$app->request->post())) {
            $model->save();
            $model->checkOther();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ForeignEventParticipants model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        $searchModel = new SearchForeignEventParticipants();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, null);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFileLoad()
    {
        $model = new LoadParticipants();

        if ($model->load(Yii::$app->request->post())) {
            $model->file = UploadedFile::getInstance($model, 'file');
            $model->save();
            return $this->redirect(['index']);
        }

        return $this->render('file-load', [
            'model' => $model,
        ]);
    }

    public function actionCheckCorrect()
    {
        $model = new ForeignEventParticipantsWork();
        $model->checkCorrect();
        return $this->redirect(['index']);
    }

    /**
     * Finds the ForeignEventParticipants model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ForeignEventParticipantsWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ForeignEventParticipantsWork::findOne($id)) !== null) {
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
            $this->redirect(['/site/error-access']);
            return false;
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}
