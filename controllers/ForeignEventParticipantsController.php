<?php

namespace app\controllers;

use app\models\components\Logger;
use app\models\components\RoleBaseAccess;
use app\models\components\UserRBAC;
use app\models\extended\LoadParticipants;
use app\models\extended\MergeParticipantModel;
use app\models\work\PersonalDataForeignEventParticipantWork;
use Yii;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\ParticipantAchievementWork;
use app\models\SearchForeignEventParticipants;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\helpers\Html;

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

    public function actionMergeParticipant()
    {
        $model = new MergeParticipantModel();
        $model->edit_model = new ForeignEventParticipantsWork();

        if ($model->load(Yii::$app->request->post()) && $model->edit_model->load(Yii::$app->request->post())) {
            $model->save();
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Объединены обучающиеся id1: '.$model->id1.' и id2: '.$model->id2);
            Yii::$app->session->setFlash('success', 'Объединение произведено успешно!');
            return $this->redirect(['view', 'id' => $model->id1]);
        }

        return $this->render('merge-participant', [
            'model' => $model,
        ]);
    }

    public function actionInfo($id1, $id2)
    {
        $p1 = ForeignEventParticipantsWork::find()->where(['id' => $id1])->one();
        $p2 = ForeignEventParticipantsWork::find()->where(['id' => $id2])->one();
        $result = '<table class="table table-striped table-bordered detail-view" style="width: 91%">';
        $result .= '<tr><td><b>Фамилия</b></td><td id="td-secondname-1" style="width: 45%">'.$p1->secondname.'</td><td><b>Фамилия</b></td><td style="width: 45%">'.$p2->secondname.'</td></tr>';
        $result .= '<tr><td><b>Имя</b></td><td id="td-firstname-1" style="width: 45%">'.$p1->firstname.'</td><td><b>Имя</b></td><td style="width: 45%">'.$p2->firstname.'</td></tr>';
        $result .= '<tr><td><b>Отчество</b></td><td id="td-patronymic-1" style="width: 45%">'.$p1->patronymic.'</td><td><b>Отчество</b></td><td style="width: 45%">'.$p2->patronymic.'</td></tr>';
        $result .= '<tr><td><b>Пол</b></td><td id="td-sex-1" style="width: 45%">'.$p1->sex.'</td><td><b>Пол</b></td><td style="width: 45%">'.$p2->sex.'</td></tr>';
        $result .= '<tr><td><b>Дата рождения</b></td><td id="td-birthdate-1" style="width: 45%">'.$p1->birthdate.'</td><td><b>Дата рождения</b></td><td style="width: 45%">'.$p2->birthdate.'</td></tr>';

        $events = TrainingGroupParticipantWork::find()->where(['participant_id' => $id1])->all();

        $eventsLink1 = '';
        $eventsLink2 = '';
        
        foreach ($events as $event)
        {

            $eventsLink1 .= date('d.m.Y', strtotime($event->trainingGroup->start_date)).' - '.date('d.m.Y', strtotime($event->trainingGroup->finish_date)).' | ';
            $eventsLink1 = $eventsLink1.Html::a('Группа '.$event->trainingGroup->number, \yii\helpers\Url::to(['training-group/view', 'id' => $event->training_group_id]));

            if ($event->trainingGroup->finish_date < date("Y-m-d"))
                $eventsLink1 .= ' (группа завершила обучение)';
            else
                $eventsLink1 .= ' <div style="background-color: green; display: inline"><font color="white"> (проходит обучение)</font></div>';

            if ($event->status === 2)
                $eventsLink1 .= ' | Переведен';

            if ($event->status === 1)
                $eventsLink1 .= ' | Отчислен';

            $eventsLink1 .= '<br>';
        }

        $events = TrainingGroupParticipantWork::find()->where(['participant_id' => $id2])->all();
        
        foreach ($events as $event)
        {
            $eventsLink2 .= date('d.m.Y', strtotime($event->trainingGroup->start_date)).' - '.date('d.m.Y', strtotime($event->trainingGroup->finish_date)).' | ';
            $eventsLink2 = $eventsLink2.Html::a('Группа '.$event->trainingGroup->number, \yii\helpers\Url::to(['training-group/view', 'id' => $event->training_group_id]));

            if ($event->trainingGroup->finish_date < date("Y-m-d"))
                $eventsLink2 .= ' (группа завершила обучение)';
            else
                $eventsLink2 .= ' <div style="background-color: green; display: inline"><font color="white"> (проходит обучение)</font></div>';

            if ($event->status === 2)
                $eventsLink2 .= ' | Переведен';

            if ($event->status === 1)
                $eventsLink2 .= ' | Отчислен';

            $eventsLink2 .= '<br>';
        }

        $result .= '<tr><td><b>Группы</b></td><td style="width: 45%">'.$eventsLink1.'</td><td><b>Группы</b></td><td style="width: 45%">'.$eventsLink2.'</td></tr>';


        $events = TeacherParticipantWork::find()->where(['participant_id' => $id1])->all();
        $eventsLink1 = '';
        foreach ($events as $event)
            $eventsLink1 = $eventsLink1.Html::a($event->foreignEvent->name, \yii\helpers\Url::to(['foreign-event/view', 'id' => $event->foreign_event_id])).'<br>';

        $events = TeacherParticipantWork::find()->where(['participant_id' => $id2])->all();
        $eventsLink2 = '';
        foreach ($events as $event)
            $eventsLink2 = $eventsLink2.Html::a($event->foreignEvent->name, \yii\helpers\Url::to(['foreign-event/view', 'id' => $event->foreign_event_id])).'<br>';

        $result .= '<tr><td><b>Мепроприятия</b></td><td style="width: 45%">'.$eventsLink1.'</td><td><b>Мепроприятия</b></td><td style="width: 45%">'.$eventsLink2.'</td></tr>';

        $achieves = ParticipantAchievementWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.participant_id' => $id1])->all();
        $achievesLink1 = '';
        foreach ($achieves as $achieveOne)
        {
            $achievesLink1 = $achievesLink1.$achieveOne->achievment.' &mdash; '.Html::a($achieveOne->teacherParticipantWork->foreignEvent->name, \yii\helpers\Url::to(['foreign-event/view', 'id' => $achieveOne->teacherParticipantWork->foreign_event_id])).
                ' ('.$achieveOne->teacherParticipantWork->foreignEvent->start_date.')'.'<br>';
        }

        $achieves = ParticipantAchievementWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.participant_id' => $id2])->all();
        $achievesLink2 = '';
        foreach ($achieves as $achieveOne)
        {
            $achievesLink2 = $achievesLink2.$achieveOne->achievment.' &mdash; '.Html::a($achieveOne->teacherParticipantWork->foreignEvent->name, \yii\helpers\Url::to(['foreign-event/view', 'id' => $achieveOne->teacherParticipantWork->foreign_event_id])).
                ' ('.$achieveOne->teacherParticipantWork->foreignEvent->start_date.')'.'<br>';
        }

        $result .= '<tr><td><b>Достижения</b></td><td style="width: 45%">'.$achievesLink1.'</td><td><b>Достижения</b></td><td style="width: 45%">'.$achievesLink2.'</td></tr>';

        $resultN = "<table class='table table-bordered'>";
        $pds = PersonalDataForeignEventParticipantWork::find()->where(['foreign_event_participant_id' => $id1])->orderBy(['id' => SORT_ASC])->all();
        foreach ($pds as $pd)
        {
            $resultN .= '<tr><td style="width: 350px">';
            if ($pd->status == 0) $resultN .= $pd->personalData->name.'</td><td style="width: 250px"><span class="badge badge-success b1">Разрешено</span></td>';
            else $resultN .= $pd->personalData->name.'</td><td style="width: 250px"><span class="badge badge-error b1">Запрещено</span></td>';
            $resultN .= '</td></tr>';
        }
        $resultN .= "</table>";

        $resultN1 = "<table class='table table-bordered'>";
        $pds = PersonalDataForeignEventParticipantWork::find()->where(['foreign_event_participant_id' => $id2])->orderBy(['id' => SORT_ASC])->all();
        foreach ($pds as $pd)
        {
            $resultN1 .= '<tr><td style="width: 350px">';
            if ($pd->status == 0) $resultN1 .= $pd->personalData->name.'</td><td style="width: 250px"><span class="badge badge-success">Разрешено</span></td>';
            else $resultN1 .= $pd->personalData->name.'</td><td style="width: 250px"><span class="badge badge-error">Запрещено</span></td>';
            $resultN1 .= '</td></tr>';
        }
        $resultN1 .= "</table>";

        $result .= '<tr><td><b>Разглашение ПД</b></td><td style="width: 45%">'.$resultN.'</td><td><b>Разглашение ПД</b></td><td style="width: 45%">'.$resultN1.'</td></tr>';

        $result .= '</table><br>';
        $result .= '<a id="fill1" style="display: block; width: 91%" onclick="FillEditForm()" class="btn btn-primary">Открыть форму редактирования</a>';

        return $result;
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
