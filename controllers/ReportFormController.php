<?php

namespace app\controllers;

use app\models\components\ExcelWizard;
use app\models\components\Logger;
use app\models\components\report\ReportWizard;
use app\models\components\RoleBaseAccess;
use app\models\components\UserRBAC;
use app\models\extended\ForeignEventReportModel;
use app\models\extended\ReportFormModel;
use app\models\extended\ManHoursReportModel;
use app\models\extended\ResultReportModel;
use app\models\extended\UsefulSideReportModel;
use app\models\work\RoleWork;
use app\models\work\VisitWork;
use kartik\mpdf\Pdf;
use Yii;
use app\models\work\PositionWork;
use app\models\SearchPosition;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * PositionController implements the CRUD actions for Position model.
 */
class ReportFormController extends Controller
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

    public function actionMpdfBlog() {
        $this->layout = 'pdf';
        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        $headers = Yii::$app->response->headers;
        $headers->add('Content-Type', 'application/pdf');
        
        $model = RoleWork::find()->where(['id' => 1])->one();
        
        //$model = $this->findModel();
        $pdf = new Pdf([
            'mode' => Pdf::MODE_UTF8, // leaner size using standard fonts
            'content' => $this->render('viewpdf', ['model'=>$model]),
            'cssFile' => '@vendor/kartik-v/yii2-mpdf/assets/kv-mpdf-bootstrap.min.css',
            'cssInline' => '.img-circle {border-radius: 50%;}', 
            'options' => [
                'title' => $model->name,
                'subject' => 'PDF'
            ],
            'methods' => [
                'SetHeader' => ['Школа брейк данса INSPIRE||inspire2.ru'],
                'SetFooter' => ['|{PAGENO}|'],
            ]
        ]);
        return $pdf->render();
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionEffectiveContract()
    {
        $model = new ForeignEventReportModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ExcelWizard::DownloadEffectiveContract($model->start_date, $model->end_date, $model->budget);
        }

        return $this->render('effective-contract', [
            'model' => $model,
        ]);
    }

    public function actionDoDop1()
    {
        $model = new ForeignEventReportModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ExcelWizard::DownloadDoDop1($model->start_date, $model->end_date, $model->budget);
        }

        return $this->render('do-dop-1', [
            'model' => $model,
        ]);
    }

    public function actionGz()
    {
        $model = new ReportFormModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ExcelWizard::DownloadGZ($model->start_date, $model->end_date, $model->method);
        }

        return $this->render('gz', [
            'model' => $model,
        ]);
    }

    public function actionGz2()
    {
        $model = new ReportFormModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ReportWizard::GenerateGZ($model->start_date, $model->end_date, $model->method == 0 ? VisitWork::ONLY_PRESENCE : VisitWork::PRESENCE_AND_ABSENCE);
        }

        return $this->render('gz-2', [
            'model' => $model,
        ]);
    }

    public function actionDo()
    {
        $model = new ReportFormModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ExcelWizard::DownloadDO($model->start_date, $model->end_date);
        }

        return $this->render('do', [
            'model' => $model,
        ]);
    }

    public function actionDod()
    {
        $model = new ReportFormModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ExcelWizard::DownloadDod($model->start_date, $model->end_date);
        }

        return $this->render('dod', [
            'model' => $model,
        ]);
    }

    public function actionDod2()
    {
        $model = new ReportFormModel();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            ReportWizard::GenerateDod($model->start_date, $model->end_date);
        }

        return $this->render('dod', [
            'model' => $model,
        ]);
    }

    public function actionTeacher()
    {
        $model = new ReportFormModel();

        if ($model->load(Yii::$app->request->post())) {
            ExcelWizard::DownloadTeacher($model->year, $model->branch);
        }

        return $this->render('teacher', [
            'model' => $model,
        ]);
    }


    //Проверка на права доступа к CRUD-операциям
    /*public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        if (!RoleBaseAccess::CheckAccess($action->controller->id, $action->id, Yii::$app->user->identity->getId(), "group")) {
            return $this->redirect(['/site/error-access']);
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }*/
}
