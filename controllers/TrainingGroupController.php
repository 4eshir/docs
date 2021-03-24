<?php

namespace app\controllers;

use app\models\common\Auditorium;
use app\models\common\Branch;
use app\models\common\OrderGroup;
use app\models\common\People;
use app\models\common\TrainingGroupLesson;
use app\models\common\TrainingGroupParticipant;
use app\models\components\Logger;
use app\models\DynamicModel;
use app\models\extended\TrainingGroupAuto;
use Yii;
use app\models\common\TrainingGroup;
use app\models\SearchTrainingGroup;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * TrainingGroupController implements the CRUD actions for TrainingGroup model.
 */
class TrainingGroupController extends Controller
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
     * Lists all TrainingGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchTrainingGroup();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrainingGroup model.
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
     * Creates a new TrainingGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TrainingGroup();
        $modelTrainingGroupParticipant = [new TrainingGroupParticipant];
        $modelTrainingGroupLesson = [new TrainingGroupLesson];
        $modelTrainingGroupAuto = [new TrainingGroupAuto];
        $modelOrderGroup = [new OrderGroup];

        if ($model->load(Yii::$app->request->post())) {

            $model->photosFile = UploadedFile::getInstances($model, 'photosFile');
            $model->presentDataFile = UploadedFile::getInstances($model, 'presentDataFile');
            $model->workDataFile = UploadedFile::getInstances($model, 'workDataFile');
            $modelTrainingGroupParticipant = DynamicModel::createMultiple(TrainingGroupParticipant::classname());
            DynamicModel::loadMultiple($modelTrainingGroupParticipant, Yii::$app->request->post());
            $model->participants = $modelTrainingGroupParticipant;
            $modelTrainingGroupLesson = DynamicModel::createMultiple(TrainingGroupLesson::classname());
            DynamicModel::loadMultiple($modelTrainingGroupLesson, Yii::$app->request->post());
            $model->lessons = $modelTrainingGroupLesson;
            $modelTrainingGroupAuto = DynamicModel::createMultiple(TrainingGroupAuto::classname());
            DynamicModel::loadMultiple($modelTrainingGroupAuto, Yii::$app->request->post());
            $model->auto = $modelTrainingGroupAuto;
            $modelOrderGroup = DynamicModel::createMultiple(OrderGroup::classname());
            DynamicModel::loadMultiple($modelOrderGroup, Yii::$app->request->post());
            $model->orders = $modelOrderGroup;
            if ($model->photosFile !== null)
                $model->uploadPhotosFile();
            if ($model->presentDataFile !== null)
                $model->uploadPresentDataFile();
            if ($model->workDataFile !== null)
                $model->uploadWorkDataFile();
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelTrainingGroupParticipant' => $modelTrainingGroupParticipant,
            'modelTrainingGroupLesson' => $modelTrainingGroupLesson,
            'modelTrainingGroupAuto' => $modelTrainingGroupAuto,
            'modelOrderGroup' => $modelOrderGroup,
        ]);
    }

    /**
     * Updates an existing TrainingGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelTrainingGroupParticipant = [new TrainingGroupParticipant];
        $modelTrainingGroupLesson = [new TrainingGroupLesson];
        $modelTrainingGroupAuto = [new TrainingGroupAuto];
        $modelOrderGroup = [new OrderGroup];

        if ($model->load(Yii::$app->request->post())) {
            $model->photosFile = UploadedFile::getInstances($model, 'photosFile');
            $model->presentDataFile = UploadedFile::getInstances($model, 'presentDataFile');
            $model->workDataFile = UploadedFile::getInstances($model, 'workDataFile');
            $modelTrainingGroupParticipant = DynamicModel::createMultiple(TrainingGroupParticipant::classname());
            DynamicModel::loadMultiple($modelTrainingGroupParticipant, Yii::$app->request->post());
            $model->participants = $modelTrainingGroupParticipant;
            $modelTrainingGroupLesson = DynamicModel::createMultiple(TrainingGroupLesson::classname());
            DynamicModel::loadMultiple($modelTrainingGroupLesson, Yii::$app->request->post());
            $model->lessons = $modelTrainingGroupLesson;
            $modelTrainingGroupAuto = DynamicModel::createMultiple(TrainingGroupAuto::classname());
            DynamicModel::loadMultiple($modelTrainingGroupAuto, Yii::$app->request->post());
            $model->auto = $modelTrainingGroupAuto;
            $modelOrderGroup = DynamicModel::createMultiple(OrderGroup::classname());
            DynamicModel::loadMultiple($modelOrderGroup, Yii::$app->request->post());
            $model->orders = $modelOrderGroup;
            if ($model->photosFile !== null)
                $model->uploadPhotosFile(10);
            if ($model->presentDataFile !== null)
                $model->uploadPresentDataFile(10);
            if ($model->workDataFile !== null)
                $model->uploadWorkDataFile(10);
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelTrainingGroupParticipant' => $modelTrainingGroupParticipant,
            'modelTrainingGroupLesson' => $modelTrainingGroupLesson,
            'modelTrainingGroupAuto' => $modelTrainingGroupAuto,
            'modelOrderGroup' => $modelOrderGroup,
        ]);
    }

    /**
     * Deletes an existing TrainingGroup model.
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
     * Finds the TrainingGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrainingGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrainingGroup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDeleteParticipant($id, $modelId)
    {
        $participant = TrainingGroupParticipant::find()->where(['id' => $id])->one();
        $participant->delete();
        return $this->redirect('index?r=training-group/update&id='.$modelId);
    }

    public function actionDeleteLesson($id, $modelId)
    {
        $participant = TrainingGroupLesson::find()->where(['id' => $id])->one();
        $participant->delete();
        return $this->redirect('index?r=training-group/update&id='.$modelId);
    }

    public function actionDeleteOrder($id, $modelId)
    {
        $order = OrderGroup::find()->where(['id' => $id])->one();
        $order->delete();
        return $this->redirect('index?r=training-group/update&id='.$modelId);
    }

    public function actionGetFile($fileName = null, $modelId = null, $type = null)
    {
        //$path = \Yii::getAlias('@upload') ;
        $file = Yii::$app->basePath . '/upload/files/group/' . $type . '/' . $fileName;
        if (file_exists($file)) {
            return \Yii::$app->response->sendFile($file);
        }
        throw new \Exception('File not found');
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {

        $model = TrainingGroup::find()->where(['id' => $modelId])->one();

        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null) {

            $result = '';
            $split = '';
            if ($type == 'photos') $split = explode(" ", $model->photos);
            if ($type == 'present_data') $split = explode(" ", $model->present_data);
            if ($type == 'work_data') $split = explode(" ", $model->work_data);
            $deleteFile = '';
            for ($i = 0; $i < count($split) - 1; $i++) {
                if ($split[$i] !== $fileName) {
                    $result = $result . $split[$i] . ' ';
                } else
                    $deleteFile = $split[$i];
            }
            if ($type == 'photos') $model->photos = $result;
            if ($type == 'present_data') $model->present_data = $result;
            if ($type == 'work_data') $model->work_data = $result;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален файл ' . $deleteFile);
        }
        return $this->redirect('index?r=training-group/update&id='.$model->id);
    }

    public function actionSubcat()
    {
        if ($id = Yii::$app->request->post('id')) {
            $operationPosts = Branch::find()
                ->where(['id' => $id])
                ->count();

            if ($operationPosts > 0) {
                $operations = Auditorium::find()
                    ->where(['branch_id' => $id])
                    ->all();
                foreach ($operations as $operation)
                    echo "<option value='" . $operation->id . "'>" . $operation->name . "</option>";
            } else
                echo "<option>-</option>";

        }
    }
}
