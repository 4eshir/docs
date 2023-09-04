<?php

namespace app\controllers\temporary;

use app\models\components\Logger;
use app\models\components\RoleBaseAccess;
use app\models\components\UserRBAC;
use app\models\extended\ForeignEventParticipantAchievement;
use Yii;
use app\models\work\CompanyWork;
use app\models\SearchCompany;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CompanyController implements the CRUD actions for Company model.
 */
class VariantsController extends Controller
{

    public function actionEventForm()
    {
        $model = new ForeignEventParticipantAchievement();

        return $this->render('event-form', [
            'model' => $model,
            'nominations' => ['one', 'two', 'three'],
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
