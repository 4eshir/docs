<?php

namespace app\controllers;

use app\models\components\Logger;
use app\models\components\RoleBaseAccess;
use app\models\components\UserRBAC;
use app\models\extended\ForeignEventReportModel;
use app\models\extended\ManHoursReportModel;
use app\models\extended\ResultReportModel;
use app\models\extended\UsefulSideReportModel;
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
        $model = new ManHoursReportModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $newModel = new ResultReportModel();
            $report = $model->generateReport();
            $newModel->header = $report[0];
            $newModel->result = $report[1];
            $newModel->debugInfo = $report[2];
            $newModel->debugInfo2 = $report[3];
            //$newModel->header = $report[3];
            return $this->render('report-result', [
                'model' => $newModel,
            ]);
        }

        return $this->render('man-hours-report', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new Position model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionUsefulSideReport()
    {
        $model = new UsefulSideReportModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $newModel = new ResultReportModel();
            $report = $model->generateReport();
            $newModel->result = $report[0];
            $newModel->debugInfo = $report[1];
            $newModel->debugInfo2 = $report[2];
            $newModel->header = $report[3];
            return $this->render('report-result', [
                'model' => $newModel,
            ]);
        }

        return $this->render('useful-side-report', [
            'model' => $model,
        ]);
    }

    public function actionForeignEventReport()
    {
        $model = new ForeignEventReportModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $newModel = new ResultReportModel();
            $report = $model->generateReportNew();
            $newModel->result = $report[0];
            $newModel->debugInfo3 = $report[1];
            $newModel->header = $report[2];
            return $this->render('report-result', [
                'model' => $newModel,
            ]);
        }

        return $this->render('foreign-event-report', [
            'model' => $model,
        ]);
    }

    public function actionGetFullReport($type)
    {
        $session = Yii::$app->session;
        $fileName = "file.csv";
        //$data = $session->get('csv1') === null ? $session->get('csv2') : $session->get('csv1');

        $data = 'test';

        if ($type == 1) $data = $session->get('csv1');
        else if ($type == 2) $data = $session->get('csv2');
        else if ($type == 3) $data = $session->get('csv3');


        header('Content-Description: File Transfer');
        header('Content-Type: application/csv;charset=UTF-8');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . mb_strlen($data));


        $temp = iconv('utf-8', 'windows-1251//TRANSLIT', $data);

        if ($session->get('csv1') === null) $session->remove('csv1');
        if ($session->get('csv2') === null) $session->remove('csv2');
        if ($session->get('csv3') === null) $session->remove('csv3');

        return $temp;
    }


    //Проверка на права доступа к CRUD-операциям
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!RoleBaseAccess::CheckAccess($action->controller->id, $action->id, Yii::$app->user->identity->getId(), "group")) {
            return $this->redirect(['/site/error-access']);
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}
