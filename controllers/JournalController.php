<?php

namespace app\controllers;

use app\models\components\ExcelWizard;
use app\models\work\GroupErrorsWork;
use app\models\work\LessonThemeWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupWork;
use app\models\work\VisitWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\GroupProjectThemesWork;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\extended\AccessTrainingGroup;
use app\models\extended\JournalModel;
use Yii;
use app\models\work\CompanyWork;
use app\models\SearchCompany;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\DynamicModel;

/**
 * CompanyController implements the CRUD actions for Company model.
 */
class JournalController extends Controller
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
     * Lists all Company models.
     * @return mixed
     */
    public function actionIndex($group_id = null)
    {
        $model = new JournalModel($group_id);

        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC])->all();
        $newLessons = array();
        foreach ($lessons as $lesson) $newLessons[] = $lesson->id;
        $visits = VisitWork::find()->joinWith(['foreignEventParticipant foreignEventParticipant'])->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['in', 'training_group_lesson_id', $newLessons])->orderBy(['foreignEventParticipant.secondname' => SORT_ASC, 'foreignEventParticipant.firstname' => SORT_ASC, 'foreignEventParticipant.patronymic' => SORT_ASC, 'trainingGroupLesson.lesson_date' => SORT_ASC, 'trainingGroupLesson.id' => SORT_ASC])->all();

        $newVisits = array();
        $newVisitsId = array();
        foreach ($visits as $visit) $newVisits[] = $visit->status;
        foreach ($visits as $visit) $newVisitsId[] = $visit->id;
        $model->visits = $newVisits;
        $model->visits_id = $newVisitsId;

        if ($model->load(Yii::$app->request->post()))
        {
            $model = new JournalModel($model->trainingGroup);

            $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC])->all();
            $newLessons = array();
            foreach ($lessons as $lesson) $newLessons[] = $lesson->id;
            $visits = VisitWork::find()->joinWith(['foreignEventParticipant foreignEventParticipant'])->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['in', 'training_group_lesson_id', $newLessons])->orderBy(['foreignEventParticipant.secondname' => SORT_ASC, 'foreignEventParticipant.firstname' => SORT_ASC, 'foreignEventParticipant.patronymic' => SORT_ASC, 'trainingGroupLesson.lesson_date' => SORT_ASC, 'trainingGroupLesson.id' => SORT_ASC])->all();

            $newVisits = array();
            $newVisitsId = array();
            foreach ($visits as $visit) $newVisits[] = $visit->status;
            foreach ($visits as $visit) $newVisitsId[] = $visit->id;
            $model->visits = $newVisits;
            $model->visits_id = $newVisitsId;

            return $this->render('index', [
                'model' => $model,
            ]);
        }
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionIndexEdit($group_id = null)
    {
        ini_set('memory_limit', -1);
        set_time_limit (0);

        $model = new JournalModel($group_id);
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC])->all();
        $newLessons = array();
        foreach ($lessons as $lesson) $newLessons[] = $lesson->id;
        $visits = VisitWork::find()->joinWith(['foreignEventParticipant foreignEventParticipant'])->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['in', 'training_group_lesson_id', $newLessons])->orderBy(['foreignEventParticipant.secondname' => SORT_ASC, 'foreignEventParticipant.firstname' => SORT_ASC, 'foreignEventParticipant.patronymic' => SORT_ASC, 'trainingGroupLesson.lesson_date' => SORT_ASC, 'trainingGroupLesson.id' => SORT_ASC])->all();


        $newVisits = array();
        $newVisitsId = array();
        foreach ($visits as $visit) $newVisits[] = $visit->status;
        foreach ($visits as $visit) $newVisitsId[] = $visit->id;
        $model->visits = $newVisits;
        $model->visits_id = $newVisitsId;
        if ($model->load(Yii::$app->request->post()))
        {
            $modelProjectThemes = DynamicModel::createMultiple(GroupProjectThemesWork::classname());
            DynamicModel::loadMultiple($modelProjectThemes, Yii::$app->request->post());
            $model->groupProjectThemes = $modelProjectThemes;
            //var_dump($model->projectThemes);

            $model->save();
            $group = TrainingGroupWork::find()->where(['id' => $group_id])->one();
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменен журнал группы '.$group->number);
            // тут должны работать проверки на ошибки
            $errorsCheck = new GroupErrorsWork();
            $errorsCheck->CheckErrorsTrainingGroupWithoutAmnesty($group_id);
            //
            return $this->redirect('index?r=journal/index&group_id='.$model->trainingGroup);
        }        $model->trainingGroup = $group_id;
        return $this->render('indexEdit', [
            'model' => $model,
            'modelProjectThemes' => (empty($modelProjectThemes)) ? [new GroupProjectThemesWork] : $modelProjectThemes,
        ]);
    }

    /**
     * Displays a single Company model.
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
     * Creates a new Company model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CompanyWork();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлена организация '.$model->name);
            Yii::$app->session->addFlash('success', 'Организация "'.$model->name.'" успешно добавлена');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Company model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменена организация '.$model->name);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Company model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->checkForeignKeys()) {
            if ($model->id == 8 || $model->id == 7)
                Yii::$app->session->addFlash('error', 'Невозможно удалить организацию. Данная организация является базовой');
            else
            {
                Yii::$app->session->addFlash('success', 'Организация "'.$model->name.'" успешно удалена');
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удалена организация '.$model->name);
                $model->delete();
            }
        }

        return $this->redirect(['index']);
    }

    public function actionAllAppearance($training_group_lesson_id, $group_id)
    {
        $visits = VisitWork::find()->where(['training_group_lesson_id' => $training_group_lesson_id])->all();
        foreach ($visits as $visit)
        {
            $visit->status = 0;
            $visit->save(false);
        }
        return $this->redirect('index.php?r=journal/index-edit&group_id='.$group_id);
    }

    public function actionAllClear($training_group_lesson_id, $group_id)
    {
        $visits = VisitWork::find()->where(['training_group_lesson_id' => $training_group_lesson_id])->all();
        foreach ($visits as $visit)
        {
            $visit->status = 3;
            $visit->save(false);
        }
        return $this->redirect('index.php?r=journal/index-edit&group_id='.$group_id);
    }

    public function actionLessonThemeClear($group_id)
    {
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $group_id])->all();
        foreach ($lessons as $lesson)
        {
            $lessonsTheme = LessonThemeWork::find()->where(['training_group_lesson_id' => $lesson->id])->all();
            foreach ($lessonsTheme as $theme)
            {
                $theme->delete();
            }
        }
        return $this->redirect('index.php?r=journal/index-edit&group_id='.$group_id);
    }

    public function actionConfirmTheme($id, $modelId)
    {
        $gpt = GroupProjectThemesWork::find()->where(['id' => $id])->one();
        $gpt->confirm = 1;
        $gpt->save();

        $model = new JournalModel($modelId);
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC])->all();
        $newLessons = array();
        foreach ($lessons as $lesson) $newLessons[] = $lesson->id;
        $visits = VisitWork::find()->joinWith(['foreignEventParticipant foreignEventParticipant'])->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['in', 'training_group_lesson_id', $newLessons])->orderBy(['foreignEventParticipant.secondname' => SORT_ASC, 'foreignEventParticipant.firstname' => SORT_ASC, 'trainingGroupLesson.lesson_date' => SORT_ASC, 'trainingGroupLesson.id' => SORT_ASC])->all();


        $newVisits = array();
        $newVisitsId = array();
        foreach ($visits as $visit) $newVisits[] = $visit->status;
        foreach ($visits as $visit) $newVisitsId[] = $visit->id;
        $model->visits = $newVisits;
        $model->visits_id = $newVisitsId;
        
        return $this->render('indexEdit', [
            'model' => $model,
            'modelProjectThemes' => [new GroupProjectThemesWork],
        ]);
    }

    public function actionDeclineTheme($id, $modelId)
    {
        $gpt = GroupProjectThemesWork::find()->where(['id' => $id])->one();

        $tgp = TrainingGroupParticipantWork::find()->where(['group_project_themes_id' => $gpt->project_theme_id])->all();

        if (count($tgp) > 0)
            Yii::$app->session->setFlash('danger', 'Невозможно отклонить тему, прикрепленную к одному или нескольким ученикам группы!');
        else
        {
            $gpt->confirm = 0;
            $gpt->save();
        }

        $model = new JournalModel($modelId);
        $lessons = TrainingGroupLessonWork::find()->where(['training_group_id' => $model->trainingGroup])->orderBy(['lesson_date' => SORT_ASC])->all();
        $newLessons = array();
        foreach ($lessons as $lesson) $newLessons[] = $lesson->id;
        $visits = VisitWork::find()->joinWith(['foreignEventParticipant foreignEventParticipant'])->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['in', 'training_group_lesson_id', $newLessons])->orderBy(['foreignEventParticipant.secondname' => SORT_ASC, 'foreignEventParticipant.firstname' => SORT_ASC, 'trainingGroupLesson.lesson_date' => SORT_ASC, 'trainingGroupLesson.id' => SORT_ASC])->all();


        $newVisits = array();
        $newVisitsId = array();
        foreach ($visits as $visit) $newVisits[] = $visit->status;
        foreach ($visits as $visit) $newVisitsId[] = $visit->id;
        $model->visits = $newVisits;
        $model->visits_id = $newVisitsId;
        
        return $this->render('indexEdit', [
            'model' => $model,
            'modelProjectThemes' => [new GroupProjectThemesWork],
        ]);
    }

    /**
     * Finds the Company model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CompanyWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CompanyWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
