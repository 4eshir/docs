<?php

namespace app\controllers;

use app\models\common\EventExternal;
use app\models\common\EventsLink;
use app\models\common\UseYears;
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
        return $this->render('view', [
            'model' => $this->findModel($id),
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
        $links = EventsLink::find()->where(['event_id' => $id])->all();
        foreach ($links as $link)
            $link->delete();
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
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
        if ($fileName !== null && !Yii::$app->user->isGuest) {
            $currentFile = Yii::$app->basePath.'/upload/files/event/'.$fileName;
            if (is_file($currentFile)) {
                header("Content-Type: application/octet-stream");
                header("Accept-Ranges: bytes");
                header("Content-Length: " . filesize($currentFile));
                header("Content-Disposition: attachment; filename=" . $fileName);
                readfile($currentFile);
                return $this->redirect('index.php?r=docs-out/create');
            };
        }
        //return $this->redirect('index.php?r=docs-out/index');
    }
}
