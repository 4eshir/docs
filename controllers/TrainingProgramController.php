<?php

namespace app\controllers;

use app\models\common\AuthorProgram;
use app\models\common\ThematicPlan;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\DynamicModel;
use app\models\extended\Author;
use Yii;
use app\models\common\TrainingProgram;
use app\models\SearchTrainingProgram;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * TrainingProgramController implements the CRUD actions for TrainingProgram model.
 */
class TrainingProgramController extends Controller
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
     * Lists all TrainingProgram models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
        $searchModel = new SearchTrainingProgram();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TrainingProgram model.
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
     * Creates a new TrainingProgram model.
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
        $model = new TrainingProgram();
        $modelAuthor = [new AuthorProgram];
        $modelThematicPlan = [new ThematicPlan];

        if ($model->load(Yii::$app->request->post())) {
            $modelAuthor = DynamicModel::createMultiple(AuthorProgram::classname());
            DynamicModel::loadMultiple($modelAuthor, Yii::$app->request->post());
            $modelThematicPlan = DynamicModel::createMultiple(ThematicPlan::classname());
            DynamicModel::loadMultiple($modelThematicPlan, Yii::$app->request->post());
            $model->authors = $modelAuthor;
            $model->thematicPlan = $modelThematicPlan;
            $model->docFile = UploadedFile::getInstance($model, 'docFile');
            $model->editDocs = UploadedFile::getInstances($model, 'editDocs');
            if ($model->docFile !== null)
                $model->uploadDocFile();
            if ($model->editDocs !== null)
                $model->uploadEditFiles();

            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelAuthor' => $modelAuthor,
            'modelThematicPlan' => $modelThematicPlan,
        ]);
    }

    /**
     * Updates an existing TrainingProgram model.
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
        $modelAuthor = [new AuthorProgram];
        $modelThematicPlan = [new ThematicPlan];

        if ($model->load(Yii::$app->request->post())) {
            $modelAuthor = DynamicModel::createMultiple(AuthorProgram::classname());
            DynamicModel::loadMultiple($modelAuthor, Yii::$app->request->post());
            $modelThematicPlan = DynamicModel::createMultiple(ThematicPlan::classname());
            DynamicModel::loadMultiple($modelThematicPlan, Yii::$app->request->post());
            $model->authors = $modelAuthor;
            $model->thematicPlan = $modelThematicPlan;
            $model->docFile = UploadedFile::getInstance($model, 'docFile');
            $model->editDocs = UploadedFile::getInstances($model, 'editDocs');
            if ($model->docFile !== null)
                $model->uploadDocFile();
            if ($model->editDocs !== null)
                $model->uploadEditFiles(10);
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelAuthor' => $modelAuthor,
            'modelThematicPlan' => $modelThematicPlan,
        ]);
    }

    /**
     * Deletes an existing TrainingProgram model.
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
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the TrainingProgram model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrainingProgram the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrainingProgram::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGetFile($fileName = null, $modelId = null, $type = null)
    {
        //$path = \Yii::getAlias('@upload') ;
        $file = Yii::$app->basePath . '/upload/files/program/' . $type . '/' . $fileName;
        if (file_exists($file)) {
            return \Yii::$app->response->sendFile($file);
        }
        throw new \Exception('File not found');
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {

        $model = TrainingProgram::find()->where(['id' => $modelId])->one();

        if ($type == 'doc')
        {
            $model->doc_file = '';
            $model->save(false);
            return $this->redirect('index?r=training-program/update&id='.$model->id);
        }


        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null) {

            $result = '';
            $split = explode(" ", $model->edit_docs);
            $deleteFile = '';
            for ($i = 0; $i < count($split) - 1; $i++) {
                if ($split[$i] !== $fileName) {
                    $result = $result . $split[$i] . ' ';
                } else
                    $deleteFile = $split[$i];
            }
            $model->edit_docs = $result;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален файл ' . $deleteFile);
        }
        return $this->redirect('index?r=training-program/update&id='.$model->id);
    }

    public function actionDeleteAuthor($peopleId, $modelId)
    {
        $resp = AuthorProgram::find()->where(['author_id' => $peopleId])->andWhere(['training_program_id' => $modelId])->one();
        if ($resp != null)
            $resp->delete();
        $model = $this->findModel($modelId);
        return $this->redirect('index.php?r=training-program/update&id='.$modelId);
    }

    public function actionDeletePlan($id, $modelId)
    {
        $plan = ThematicPlan::find()->where(['id' => $id])->one();
        $plan->delete();
        return $this->redirect('index?r=training-program/update&id='.$modelId);
    }
}
