<?php

namespace app\controllers;

use Yii;
use app\models\common\DocumentOut;
use app\models\SearchDocumentOut;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

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
            $model->document_number = $model->getDocumentNumber();
            $model->Scan = '';
            $model->signed_id = 1;
            $model->executor_id = 1;

            $model->register_id = Yii::$app->user->identity->getId();
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->applicationFiles = UploadedFile::getInstances($model, 'applicationFiles');


            if ($model->validate()) {
                if ($model->scanFile != null)
                    $model->uploadScanFile();
                if ($model->applicationFiles != null)
                    $model->uploadApplicationFiles();
                $model->save(false);
                return $this->redirect('index.php?r=docs-out/index');
            }
        }




        return $this->render('/docs-out/create', [
            'model' => $model,
        ]);
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
            if ($model->validate()) {
                if ($model->scanFile != null)
                    $model->uploadScanFile();
                if ($model->applicationFiles != null)
                    $model->uploadApplicationFiles(10);
                $model->save(false);

                return $this->redirect('index.php?r=docs-out/index');
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
        $this->findModel($id)->delete();

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

    public function actionGetFile($fileName = null, $modelId = null)
    {

        if ($fileName !== null && !Yii::$app->user->isGuest) {
            $currentFile = Yii::$app->basePath.'/upload/files/'.$fileName;
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
