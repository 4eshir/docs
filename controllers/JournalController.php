<?php

namespace app\controllers;

use app\models\common\TrainingGroupLesson;
use app\models\common\Visit;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\extended\AccessTrainingGroup;
use app\models\extended\JournalModel;
use Yii;
use app\models\common\Company;
use app\models\SearchCompany;
use yii\db\Query;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id) && !AccessTrainingGroup::CheckAccess(Yii::$app->user->identity->getId())) {
            return $this->render('/site/error');
        }
        $model = new JournalModel($group_id);
        if ($model->load(Yii::$app->request->post()))
        {
            $model = new JournalModel($model->trainingGroup);
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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id) && !AccessTrainingGroup::CheckAccess(Yii::$app->user->identity->getId(), $group_id)) {
            return $this->render('/site/error');
        }
        $model = new JournalModel($group_id);
        $lessons = TrainingGroupLesson::find()->where(['training_group_id' => $group_id])->all();
        $newLessons = array();
        foreach ($lessons as $lesson) $newLessons[] = $lesson->id;
        $visits = Visit::find()->joinWith(['foreignEventParticipant foreignEventParticipant'])->joinWith(['trainingGroupLesson trainingGroupLesson'])->where(['in', 'training_group_lesson_id', $newLessons])->orderBy(['foreignEventParticipant.secondname' => SORT_ASC, 'trainingGroupLesson.lesson_date' => SORT_ASC, 'trainingGroupLesson'.'id' => SORT_ASC])->all();
        $newVisits = array();
        foreach ($visits as $visit) $newVisits[] = $visit->status;
        $model->visits = $newVisits;
        if ($model->load(Yii::$app->request->post()))
        {

            $model->save();
            return $this->redirect('index?r=journal/index&group_id='.$model->trainingGroup);
        }
        $model->trainingGroup = $group_id;
        return $this->render('indexEdit', [
            'model' => $model,
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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, 'Add')) {
            return $this->render('/site/error');
        }
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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, 'Add')) {
            return $this->render('/site/error');
        }
        $model = new Company();

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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, 'Add')) {
            return $this->render('/site/error');
        }
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
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, 'Add')) {
            return $this->render('/site/error');
        }
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

    /**
     * Finds the Company model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Company the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Company::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
