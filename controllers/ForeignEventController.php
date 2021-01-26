<?php

namespace app\controllers;

use app\models\common\Responsible;
use app\models\DynamicModel;
use app\models\extended\ForeignEventParticipantsExtended;
use Yii;
use app\models\common\ForeignEvent;
use app\models\SearchForeignEvent;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * ForeignEventController implements the CRUD actions for ForeignEvent model.
 */
class ForeignEventController extends Controller
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
     * Lists all ForeignEvent models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchForeignEvent();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ForeignEvent model.
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
     * Creates a new ForeignEvent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ForeignEvent();
        $modelParticipants = [new ForeignEventParticipantsExtended];

        if ($model->load(Yii::$app->request->post())) {

            $modelParticipants = DynamicModel::createMultiple(ForeignEventParticipantsExtended::classname());
            DynamicModel::loadMultiple($modelParticipants, Yii::$app->request->post());
            $model->participants = $modelParticipants;
            $i = 0;
            foreach ($modelParticipants as $modelParticipantOne)
            {
                $modelParticipantOne->file = \yii\web\UploadedFile::getInstance($modelParticipantOne, "[{$i}]file");
                if ($modelParticipantOne->file !== null) $modelParticipantOne->uploadFile($model->name, $model->start_date);

            }

            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelParticipants' => $modelParticipants
        ]);
    }

    /**
     * Updates an existing ForeignEvent model.
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
     * Deletes an existing ForeignEvent model.
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
     * Finds the ForeignEvent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ForeignEvent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ForeignEvent::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
