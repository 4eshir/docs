<?php

namespace app\controllers;

use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\extended\ManHoursReportModel;
use app\models\extended\ResultReportModel;
use Yii;
use app\models\work\PositionWork;
use app\models\SearchPosition;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PositionController implements the CRUD actions for Position model.
 */
class ReportController extends Controller
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
     * Displays a single Position model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */

    public function actionReportResult($result)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id))
            return $this->render('/site/error');

        $model = new ResultReportModel();
        $model->result = $result;
        return $this->render('report-result', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Position model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionManHoursReport()
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id))
            return $this->render('/site/error');

        $model = new ManHoursReportModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $newModel = new ResultReportModel();
            $newModel->result = $model->generateReport();
            return $this->render('report-result', [
                'model' => $newModel,
            ]);
        }

        return $this->render('man-hours-report', [
            'model' => $model,
        ]);
    }
}
