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
            $model->register_id = Yii::$app->user->identity->getId();
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->applicationFiles = UploadedFile::getInstances($model, 'applicationFiles');
            if ($model->uploadApplicationFiles())
            {
                $model->applications = $model->applicationFiles[0].' ';
                for ($i = 1; $i < count($model->applicationFiles); $i++)
                    $model->applications = $model->applications.' '.$model->applicationFiles[$i];
            }


            $model->Scan = 'init';
            if ($model->validate(false)) {
                $path = '@app/upload/files/';

                $model->Scan = $model->scanFile;
                $model->save(false);
                $model->scanFile->saveAs( $path . $model->scanFile);
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
            if ($model->validate(false)) {
                $path = '@app/upload/files/';

                if ($model->scanFile !== null)
                    $model->Scan = $model->scanFile;

                $model->save(false);
                if ($model->scanFile !== null)
                    $model->scanFile->saveAs( $path . $model->scanFile);
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

    public function actionGetFile($fileName = null)
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
