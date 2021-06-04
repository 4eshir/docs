<?php

namespace app\controllers;

use app\models\common\Auditorium;
use app\models\common\Branch;
use app\models\common\LegacyResponsible;
use app\models\components\Logger;
use Yii;
use app\models\common\LocalResponsibility;
use app\models\SearchLocalResponsibility;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * LocalResponsibilityController implements the CRUD actions for LocalResponsibility model.
 */
class LocalResponsibilityController extends Controller
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
     * Lists all LocalResponsibility models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchLocalResponsibility();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single LocalResponsibility model.
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
     * Creates a new LocalResponsibility model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new LocalResponsibility();

        if ($model->load(Yii::$app->request->post())) {
            $model->filesStr = UploadedFile::getInstances($model, 'filesStr');
            if ($model->filesStr !== null)
                $model->uploadFiles();
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing LocalResponsibility model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $subModel = LegacyResponsible::find()->where(['people_id' => $model->people_id])->andWhere(['responsibility_type_id' => $model->responsibility_type_id])
            ->andWhere(['branch_id' => $model->branch_id])->andWhere(['auditorium_id' => $model->auditorium_id])->one();

        if ($subModel !== null)
        {
            $model->start_date = $subModel->start_date;
            $model->order_id = $subModel->order_id;
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->filesStr = UploadedFile::getInstances($model, 'filesStr');
            if ($model->end_date !== "")
                $model->detachResponsibility();
            if ($model->filesStr !== null)
                $model->uploadFiles(10);
            $model->save();
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing LocalResponsibility model.
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

    public function actionGetFile($fileName = null, $modelId = null, $type = null)
    {
        $file = Yii::$app->basePath . '/upload/files/responsibility/' . $fileName;
        if (file_exists($file)) {
            return \Yii::$app->response->sendFile($file);
        }
        throw new \Exception('File not found');
        //return $this->redirect('index.php?r=docs-out/index');
    }

    public function actionDeleteFile($fileName = null, $modelId = null)
    {

        $model = LocalResponsibility::find()->where(['id' => $modelId])->one();

        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null) {

            $result = '';
            $split = explode(" ", $model->files);
            $deleteFile = '';
            for ($i = 0; $i < count($split) - 1; $i++) {
                if ($split[$i] !== $fileName) {
                    $result = $result . $split[$i] . ' ';
                } else
                    $deleteFile = $split[$i];
            }
            $model->files = $result;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален файл ' . $deleteFile);
        }
        return $this->redirect('index?r=local-responsibility/update&id='.$model->id);
    }

    public function actionSubcat()
    {
        if ($id = Yii::$app->request->post('id')) {
            $operationPosts = Branch::find()
                ->where(['id' => $id])
                ->count();

            if ($operationPosts > 0) {
                $operations = Auditorium::find()
                    ->where(['branch_id' => $id])
                    ->all();
                echo "<option value=>--</option>";
                foreach ($operations as $operation)
                    echo "<option value='" . $operation->id . "'>" . $operation->name . "</option>";
            } else
                echo "<option>-</option>";

        }
    }

    /**
     * Finds the LocalResponsibility model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return LocalResponsibility the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = LocalResponsibility::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
