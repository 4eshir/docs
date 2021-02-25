<?php

namespace app\controllers;

use app\models\common\EventExternal;
use app\models\common\EventParticipants;
use app\models\common\EventsLink;
use app\models\common\UseYears;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\DynamicModel;
use Yii;
use app\models\common\Event;
use app\models\SearchEvent;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * EventController implements the CRUD actions for Event model.
 */
class EventController extends Controller
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
     * Lists all Event models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id)) {
            return $this->render('/site/error');
        }
        $searchModel = new SearchEvent();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Event model.
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
        $model = $this->findModel($id);
        $eventP = EventParticipants::find()->where(['event_id' => $model->id])->one();
        $model->childs = $eventP->child_participants;
        $model->teachers = $eventP->teacher_participants;
        $model->others = $eventP->other_participants;
        $model->leftAge = $eventP->age_left_border;
        $model->rightAge = $eventP->age_right_border;
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Event model.
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
        $model = new Event();
        $modelEventsLinks = [new EventsLink];

        if ($model->load(Yii::$app->request->post())) {
            $model->protocolFile = UploadedFile::getInstances($model, 'protocolFile');
            $model->reportingFile = UploadedFile::getInstances($model, 'reportingFile');
            $model->photoFiles = UploadedFile::getInstances($model, 'photoFiles');
            $model->otherFiles = UploadedFile::getInstances($model, 'otherFiles');
            $model->protocol = '';
            $model->reporting_doc = '';
            $model->photos = '';
            $model->other_files = '';
            if ($model->order_id == '') $model->order_id = null;
            if ($model->regulation_id == '') $model->regulation_id = null;

            $modelEventsLinks = DynamicModel::createMultiple(EventsLink::classname());
            DynamicModel::loadMultiple($modelEventsLinks, Yii::$app->request->post());
            $model->eventsLink = $modelEventsLinks;

            if ($model->validate(false))
            {
                if ($model->protocolFile !== null)
                    $model->uploadProtocolFile();
                if ($model->reportingFile !== null)
                    $model->uploadReportingFile();
                if ($model->photoFiles !== null)
                    $model->uploadPhotosFiles();
                if ($model->otherFiles !== null)
                    $model->uploadOtherFiles();
                $model->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлено мероприятие '.$model->name);
            }
            else
            {
                var_dump($model->getErrors());
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelEventsLinks' => (empty($modelEventsLinks)) ? [new EventsLink] : $modelEventsLinks,
        ]);
    }

    /**
     * Updates an existing Event model.
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
        $modelEventsLinks = [new EventsLink];
        $eventP = EventParticipants::find()->where(['event_id' => $model->id])->one();
        $model->childs = $eventP->child_participants;
        $model->childs_rst = $eventP->child_rst_participants;
        $model->teachers = $eventP->teacher_participants;
        $model->others = $eventP->other_participants;
        $model->leftAge = $eventP->age_left_border;
        $model->rightAge = $eventP->age_right_border;
        $model->old_name = $model->name;

        if ($model->load(Yii::$app->request->post())) {

            if ($model->validate(false))
            {
                $model->protocolFile = UploadedFile::getInstances($model, 'protocolFile');
                $model->reportingFile = UploadedFile::getInstances($model, 'reportingFile');
                $model->photoFiles = UploadedFile::getInstances($model, 'photoFiles');
                $model->otherFiles = UploadedFile::getInstances($model, 'otherFiles');

                $modelEventsLinks = DynamicModel::createMultiple(EventsLink::classname());
                DynamicModel::loadMultiple($modelEventsLinks, Yii::$app->request->post());
                $model->eventsLink = $modelEventsLinks;

                if ($model->validate(false))
                {
                    if ($model->protocolFile !== null)
                        $model->uploadProtocolFile(10);
                    if ($model->reportingFile !== null)
                        $model->uploadReportingFile(10);
                    if ($model->photoFiles !== null)
                        $model->uploadPhotosFiles(10);
                    if ($model->otherFiles !== null)
                        $model->uploadOtherFiles(10);
                    $model->save(false);
                    Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменено мероприятие '.$model->name);
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelEventsLinks' => (empty($modelEventsLinks)) ? [new EventsLink] : $modelEventsLinks,
        ]);
    }

    /**
     * Deletes an existing Event model.
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
        $eventP = EventParticipants::find()->where(['event_id' => $id])->one();
        $eventP->delete();
        $links = EventsLink::find()->where(['event_id' => $id])->all();
        $name = $this->findModel($id)->name;
        foreach ($links as $link)
            $link->delete();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удалено мероприятие '.$name);
        $this->findModel($id)->delete();


        return $this->redirect(['index']);
    }

    public function actionDeleteExternalEvent($id, $modelId)
    {
        $eventsLink = EventsLink::find()->where(['id' => $id])->one();
        $eventsLink->delete();
        return $this->redirect('index?r=event/update&id='.$modelId);
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {

        $model = Event::find()->where(['id' => $modelId])->one();

        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null)
        {
            $fileCell = $model->protocol;
            if ($type == 'photos') $fileCell = $model->photos;
            if ($type == 'report') $fileCell = $model->reporting_doc;
            if ($type == 'other') $fileCell = $model->other_files;
            $result = '';
            $split = explode(" ", $fileCell);
            $deleteFile = '';
            for ($i = 0; $i < count($split) - 1; $i++)
            {
                if ($split[$i] !== $fileName)
                {
                    $result = $result.$split[$i].' ';
                }
                else
                    $deleteFile = $split[$i];
            }

            if ($type == null) $model->protocol = $result;
            if ($type == 'photos') $model->photos = $result;
            if ($type == 'report') $model->reporting_doc = $result;
            if ($type == 'other') $model->other_files = $result;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален файл '.$deleteFile);
        }
        return $this->redirect('index.php?r=event/update&id='.$modelId);
    }

    /**
     * Finds the Event model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Event the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Event::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //-------------------------

    public function actionGetFile($fileName = null)
    {
        $file = Yii::$app->basePath . '/upload/files/event/' . $fileName;
        if (file_exists($file)) {
            return \Yii::$app->response->sendFile($file);
        }
        throw new \Exception('File not found');
        //return $this->redirect('index.php?r=docs-out/index');
    }
}
