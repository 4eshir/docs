<?php

namespace app\controllers;

use app\models\components\RoleBaseAccess;
use app\models\strategies\FileDownloadStrategy\FileDownloadServer;
use app\models\strategies\FileDownloadStrategy\FileDownloadYandexDisk;
use app\models\work\DocumentOrderWork;
use app\models\work\ExpireWork;
use app\models\work\ForeignEventErrorsWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\ParticipantFilesWork;
use app\models\work\ResponsibleWork;
use app\models\work\TeamNameWork;
use app\models\work\TeamWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TeacherParticipantBranchWork;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\DynamicModel;
use app\models\extended\ForeignEventParticipantsExtended;
use app\models\extended\LoadParticipants;
use app\models\extended\ParticipantsAchievementExtended;
use app\models\extended\TeamModel;
use Yii;
use app\models\work\ForeignEventWork;
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
        $model = new ForeignEventWork();
        $modelParticipants = [new ForeignEventParticipantsExtended];
        $modelAchievement = [new ParticipantsAchievementExtended];

        if ($model->load(Yii::$app->request->post())) {
            $model->creator_id = Yii::$app->user->identity->getId();
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
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлен учет достижений ' . $model->name);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelParticipants' => $modelParticipants,
            'modelAchievement' => $modelAchievement,
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
                {
                    $modelParticipantOne->file = \yii\web\UploadedFile::getInstance($modelParticipantOne, "[{$i}]file");
                    if ($modelParticipantOne->file !== null) $modelParticipantOne->uploadFile($model->name, $model->start_date);
                }
                else
                {
                    $modelParticipantOne->uploadCopyFile($modelParticipantOne->file);
                }
                $i++;
            }
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменена карточка учета достижений ' . $model->name);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelParticipants' => $modelParticipants,
            'modelAchievement' => $modelAchievement,
        ]);
    }

    public function actionUpdateParticipant($id)
    {
        $model = TeacherParticipantWork::find()->where(['id' => $id])->one();
        $model->getTeam();
        $model->branchs = $model->getBranchs();
        $back = 'event';
        if ($model->load(Yii::$app->request->post()))
        {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file !== null)
                $model->uploadParticipantFiles();
            $model->save(false);

        }

        return $this->render('update-participant',[
            'model' => $model,
            'back' => $back,
        ]);
    }

    public function actionUpdateAchievement($id, $modelId)
    {
        $model = ParticipantAchievementWork::find()->where(['id' => $id])->one();
        
        if ($model->load(Yii::$app->request->post()))
        {

           
            $model->save();
            $model = ForeignEventWork::find()->where(['id' => $modelId])->one();
            $modelParticipants = [new ForeignEventParticipantsExtended];
            $modelAchievement = [new ParticipantsAchievementExtended];
            return $this->render('update',[
                'model' => $model,
                'modelParticipants' => $modelParticipants,
                'modelAchievement' => $modelAchievement,
            ]);
        }
        return $this->render('update-achievement',[
            'model' => $model,
        ]);
    }

    public function actionCreateTeam()
    {
        var_dump('lol');
    }

    public function actionAmnesty ($id)
    {
        $errorsAmnesty = new ForeignEventErrorsWork();
        $errorsAmnesty->ForeignEventAmnesty($id);
        return $this->redirect('index?r=foreign-event/view&id='.$id);
    }

    public function actionDeleteParticipant($id, $model_id)
    {
        /*$part = TeacherParticipantWork::find()->where(['id' => $id])->one();
        $p_id = $part->participant_id;
        $branchs = TeacherParticipantBranchWork::find()->where(['teacher_participant_id' => $id])->all();
        foreach ($branchs as $branch) $branch->delete();
        $part->delete();
        $files = ParticipantFilesWork::find()->where(['participant_id' => $p_id])->one();
        if ($files !== null)
            $files->delete();
        $team = TeamWork::find()->where(['participant_id' => $p_id])->all();
        foreach ($team as $one)
            $team->delete();
        return $this->redirect('index.php?r=foreign-event/update&id='.$model_id);*/
    }

    public function actionDeleteAchievement($id, $model_id)
    {
        $part = ParticipantAchievementWork::find()->where(['id' => $id])->one();
        if ($part->team_name_id != null)
        {
            $otherPartTeam = ParticipantAchievementWork::find()->where(['team_name_id' => $part->team_name_id])->andWhere(['!=', 'id', $id])->all();
            foreach ($otherPartTeam as $one) $one->delete();
        }
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
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удалено мероприятие' . $this->findModel($id)->name);
        $this->findModel($id)->delete();
        
        return $this->redirect(['index']);
    }


    public function actionGetFile($fileName = null, $modelId = null, $type = null)
    {

        $filePath = '/upload/files/'.Yii::$app->controller->id;
        $filePath .= $type == null ? '/' : '/'.$type.'/';

        $downloadServ = new FileDownloadServer($filePath, $fileName);
        $downloadYadi = new FileDownloadYandexDisk($filePath, $fileName);

        $downloadServ->LoadFile();
        if (!$downloadServ->success) $downloadYadi->LoadFile();
        else return \Yii::$app->response->sendFile($downloadServ->file);

        if (!$downloadYadi->success) throw new \Exception('File not found');
        else {

            $fp = fopen('php://output', 'r');

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $downloadYadi->filename);
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . $downloadYadi->file->size);

            $downloadYadi->file->download($fp);

            fseek($fp, 0);
        }
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {

        $model = ForeignEventWork::find()->where(['id' => $modelId])->one();

        if ($type == 'docs') {
            $model->docs_achievement = '';
            $model->save(false);
            return $this->redirect('index?r=foreign-event/update&id=' . $model->id);
        }
        if ($type == 'participants')
        {
            $partFile = ParticipantFilesWork::find()->where(['id' => $modelId])->one();
            $tp = TeacherParticipantWork::find()->where(['participant_id' => $partFile->participant_id])->andWhere(['foreign_event_id' => $partFile->foreign_event_id])->one();
            $tModelId = $partFile->foreign_event_id;
            $partFile->delete();
            return $this->redirect('index?r=foreign-event/update-participant&id=' . $tp->id.'&modelId='.$tModelId);
        }

    }



    /**
     * Finds the ForeignEvent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ForeignEventWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ForeignEventWork::findOne($id)) !== null) {
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

    public function actionFormOrder()
    {
        $model = new ForeignEventWork();

        if ($model->load(Yii::$app->request->post())) {
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлен учет достижений ' . $model->name);
        }

        return $this->render('form-order', [
            'model' => $model,
        ]);
    }
}
