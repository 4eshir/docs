<?php

namespace app\controllers;

use app\models\components\PdfWizard;
use Yii;
use app\models\common\Certificat;
use app\models\work\CertificatWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\SearchCertificat;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\models\components\RoleBaseAccess;

use yii\helpers\FileHelper;

/**
 * CertificatController implements the CRUD actions for Certificat model.
 */
class CertificatController extends Controller
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
     * Lists all Certificat models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchCertificat();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionMainIndex()
    {
        return $this->render('main-index');
    }

    /**
     * Displays a single Certificat model.
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
     * Creates a new Certificat model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CertificatWork();

        if ($model->load(Yii::$app->request->post())) {
            $model->mass_save();
            return $this->redirect('index?r=certificat/download');
            //return $this->redirect(['index', 'ok' => 'ok']);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }



    /**
     * Updates an existing Certificat model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Certificat model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Certificat model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Certificat the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CertificatWork::find()->where(['id' => $id])->one()) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionGenerationPdf($certificat_id)
    {
        PdfWizard::DownloadCertificat($certificat_id, 'download');
    }

    public function actionSendPdf($certificat_id)
    {
        FileHelper::createDirectory(Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'_s/');

        $certificat = CertificatWork::find()->where(['id' => $certificat_id])->one();
        $participant = TrainingGroupParticipantWork::find()->where(['id' => $certificat->training_group_participant_id])->one();

        $name = PdfWizard::DownloadCertificat($certificat->id, 'server', Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'_s/');
        $result = Yii::$app->mailer->compose()
        ->setFrom('noreply@schooltech.ru')
        ->setTo($participant->participant->email)
        ->setSubject('Сертификат об успешном прохождении программы ДО')
        ->setHtmlBody('Сертификат находится в прикрепленном файле.<br><br><br>Пожалуйста, обратите внимание, что это сообщение было сгенерировано и отправлено в автоматическом режиме. Не отвечайте на него. По всем вопросам обращайтесь по телефону 44-24-28 (единый номер).')
        ->attach(Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'_s/' . $name . '.pdf')
        ->send();
        if ($result)
        {
            $certificat->status = 1;
            Yii::$app->session->setFlash('success', 'Сертификат успешно отправлен на адрес: '.$participant->participant->email);
        }
        else
        {
            $certificat->status = 2;
            Yii::$app->session->setFlash('danger', 'Не удалось отправить сертификат на указанный адрес: '.$participant->participant->email);
        }
        $certificat->save();

        FileHelper::removeDirectory(Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'_s/');




        return $this->redirect('index?r=certificat/view&id='.$certificat_id);
    }

    public function actionDownload()
    {
        $cert = new CertificatWork();
        $cert->archiveDownload();
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
}
