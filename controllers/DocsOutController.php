<?php

namespace app\controllers;

use app\models\common\Position;
use Yii;
use app\models\common\DocumentOut;
use app\models\SearchDocumentOut;
use yii\filters\AccessControl;
use yii\helpers\Json;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use \kartik\depdrop\DepDrop;

/**
 * DocsOutController implements the CRUD actions for DocumentOut model.
 */
class DocsOutController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => [],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all DocumentOut models.
     * @return mixed
     */
    public function actionIndex()
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        $searchModel = new SearchDocumentOut();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DocumentOut model.
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
     * Creates a new DocumentOut model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DocumentOut();
        $model->document_name = "default";

        if($model->load(Yii::$app->request->post()))
        {
            $model->applications = '';
            $model->doc = '';
            $model->getDocumentNumber();
            $model->Scan = '';

            $model->register_id = Yii::$app->user->identity->getId();
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->applicationFiles = UploadedFile::getInstances($model, 'applicationFiles');
            $model->docFiles = UploadedFile::getInstances($model, 'docFiles');


            if ($model->validate(false)) {
                if ($model->scanFile != null)
                    $model->uploadScanFile();
                if ($model->applicationFiles != null)
                    $model->uploadApplicationFiles();
                if ($model->docFiles != null)
                    $model->uploadDocFiles();
                $model->save(false);
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }




        return $this->render('/docs-out/create', [
            'model' => $model,
        ]);
    }

    public function actionCreateReserve()
    {
        $model = new DocumentOut();

        $model->document_theme = 'Резерв';
        $model->document_name = '';
        $model->document_date = end(DocumentOut::find()->orderBy(['document_number' => SORT_ASC, 'document_postfix' => SORT_ASC])->all())->document_date;
        $model->sent_date = '1999-01-01';
        $model->Scan = '';
        $model->applications = '';
        $model->register_id = Yii::$app->user->identity->getId();
        $model->getDocumentNumber();
        Yii::$app->session->addFlash('success', 'Резерв успешно добавлен');
        $model->save(false);

        return $this->redirect('index.php?r=docs-out/index');
    }

    /**
     * Updates an existing DocumentOut model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $model->scanFile = $model->Scan;

        if($model->load(Yii::$app->request->post()))
        {
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->applicationFiles = UploadedFile::getInstances($model, 'applicationFiles');
            if ($model->validate(false)) {
                if ($model->scanFile != null)
                    $model->uploadScanFile();
                if ($model->applicationFiles != null)
                    $model->uploadApplicationFiles(10);
                if ($model->docFiles != null)
                    $model->uploadDocFiles(10);
                $model->save(false);

                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DocumentOut model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $name = $this->findModel($id)->document_theme;
        $this->findModel($id)->delete();
        Yii::$app->session->addFlash('success', 'Документ "'.$name.'" успешно удален');
        return $this->redirect(['index']);
    }

    /**
     * Finds the DocumentOut model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DocumentOut the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentOut::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDeleteFile($fileName = null, $modelId = null)
    {

        $model = DocumentOut::find()->where(['id' => $modelId])->one();

        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null)
        {

            $result = '';
            $split = explode(" ", $model->applications);
            for ($i = 0; $i < count($split) - 1; $i++)
            {
                if ($split[$i] !== $fileName)
                {
                    $result = $result.$split[$i].' ';
                }
            }
            $model->applications = $result;
            $model->save();
        }
        return $this->render('update', [
            'model' => $this->findModel($modelId),
        ]);
    }

    public function actionGetFile($fileName = null, $modelId = null, $type = null)
    {

        if ($fileName !== null && !Yii::$app->user->isGuest) {
            $currentFile = '';
            if ($type == 'app')
                $currentFile = Yii::$app->basePath.'/upload/files/document_out/apps/'.$fileName;
            else
                $currentFile = Yii::$app->basePath.'/upload/files/document_out/scan/'.$fileName;
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

    public function actionPositions()
    {
        $parents = Yii::$app->request->post('depdrop_parents', null);
        if ($parents != null) {
            $positions = $parents[0];
            $arr = Position::find()->where(['id' => $positions->position_id])->one();
            return Json::encode(array(
                'output' => $arr,
                'selected' => $positions
            ));
        }

        return Json::encode(['output'=>'', 'selected'=>'']);
    }

}
