<?php

namespace app\controllers;

use app\models\common\ForeignEventParticipants;
use app\models\common\ParticipantAchievement;
use app\models\common\ParticipantFiles;
use app\models\common\Responsible;
use app\models\common\TeacherParticipant;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\DynamicModel;
use app\models\extended\ForeignEventParticipantsExtended;
use app\models\extended\ParticipantsAchievementExtended;
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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
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
     * Creates a new ForeignEvent model.
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
        $model = new ForeignEvent();
        $modelParticipants = [new ForeignEventParticipantsExtended];
        $modelAchievement = [new ParticipantsAchievementExtended];

        if ($model->load(Yii::$app->request->post())) {

            $modelParticipants = DynamicModel::createMultiple(ForeignEventParticipantsExtended::classname());
            DynamicModel::loadMultiple($modelParticipants, Yii::$app->request->post());
            $modelAchievement = DynamicModel::createMultiple(ParticipantsAchievementExtended::classname());
            DynamicModel::loadMultiple($modelAchievement, Yii::$app->request->post());
            $model->participants = $modelParticipants;
            $model->achievement = $modelAchievement;

            $model->docsAchievement = UploadedFile::getInstance($model, 'docs_achievement');
            if ($model->docsAchievement !== null)
                $model->uploadAchievementsFile();

            $i = 0;
            foreach ($modelParticipants as $modelParticipantOne)
            {
                $modelParticipantOne->file = \yii\web\UploadedFile::getInstance($modelParticipantOne, "[{$i}]file");
                if ($modelParticipantOne->file !== null) $modelParticipantOne->uploadFile($model->name, $model->start_date);
                $i++;
            }

            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelParticipants' => $modelParticipants,
            'modelAchievement' => $modelAchievement
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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
        $model = $this->findModel($id);
        $modelParticipants = [new ForeignEventParticipantsExtended];
        $modelAchievement = [new ParticipantsAchievementExtended];

        if ($model->load(Yii::$app->request->post())) {
            $modelParticipants = DynamicModel::createMultiple(ForeignEventParticipantsExtended::classname());
            DynamicModel::loadMultiple($modelParticipants, Yii::$app->request->post());
            $modelAchievement = DynamicModel::createMultiple(ParticipantsAchievementExtended::classname());
            DynamicModel::loadMultiple($modelAchievement, Yii::$app->request->post());
            $model->participants = $modelParticipants;
            $model->achievement = $modelAchievement;

            $model->docsAchievement = UploadedFile::getInstance($model, 'docsAchievement');
            if ($model->docsAchievement !== null)
                $model->uploadAchievementsFile();

            $i = 0;
            foreach ($modelParticipants as $modelParticipantOne)
            {
                if (strlen($modelParticipantOne->file) == 0)
                    $modelParticipantOne->file = \yii\web\UploadedFile::getInstance($modelParticipantOne, "[{$i}]file");
                else
                    $modelParticipantOne->file = \yii\web\UploadedFile::getInstance($modelParticipantOne, $modelParticipantOne->file);
                if ($modelParticipantOne->file !== null) $modelParticipantOne->uploadFile($model->name, $model->start_date);
                $i++;
            }
            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelParticipants' => $modelParticipants,
            'modelAchievement' => $modelAchievement
        ]);
    }

    public function actionDeleteParticipant($id, $model_id)
    {
        $part = TeacherParticipant::find()->where(['id' => $id])->one();
        $p_id = $part->participant_id;
        $part->delete();
        $files = ParticipantFiles::find()->where(['participant_id' => $p_id])->one();
        $files->delete();
        return $this->redirect('index.php?r=foreign-event/update&id='.$model_id);
    }

    public function actionDeleteAchievement($id, $model_id)
    {
        $part = ParticipantAchievement::find()->where(['id' => $id])->one();
        $part->delete();
        return $this->redirect('index.php?r=foreign-event/update&id='.$model_id);
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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }


    public function actionGetFile($fileName = null, $modelId = null, $type = null)
    {

        if ($fileName !== null && !Yii::$app->user->isGuest) {
            $currentFile = Yii::$app->basePath.'/upload/files/foreign_event/'.$type.'/'.$fileName;
            if (is_file($currentFile)) {
                header("Content-Type: application/octet-stream");
                header("Accept-Ranges: bytes");
                header("Content-Length: " . filesize($currentFile));
                header("Content-Disposition: attachment; filename=" . $fileName);
                readfile($currentFile);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Загружен файл '.$fileName);
                return $this->redirect('index.php?r=foreign-event/create');
            };
        }
        //return $this->redirect('index.php?r=docs-out/index');
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
