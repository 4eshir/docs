<?php

namespace app\controllers;

use app\models\components\FileWizard;
use app\models\components\RoleBaseAccess;
use app\models\strategies\FileDownloadStrategy\FileDownloadServer;
use app\models\strategies\FileDownloadStrategy\FileDownloadYandexDisk;
use Yii;
use app\models\common\MaterialObject;
use app\models\work\MaterialObjectWork;
use app\models\work\KindCharacteristicWork;
use app\models\work\ObjectCharacteristicWork;
use app\models\SearchMaterialObject;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;

/**
 * MaterialObjectController implements the CRUD actions for MaterialObject model.
 */
class MaterialObjectController extends Controller
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
     * Lists all MaterialObject models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchMaterialObject();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single MaterialObject model.
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
     * Creates a new MaterialObject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new MaterialObjectWork();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing MaterialObject model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->expiration_date !== 0)
            $model->expirationDate = date("Y-m-d", strtotime($model->create_date.'+ '.$model->expiration_date.' days'));
        else
            $model->expiration_date = 0;

        if ($model->load(Yii::$app->request->post()) && $model->save()) {

            return $this->redirect(['view', 'id' => $model->id]);
        }
        return $this->render('update', [
            'model' => $model,
        ]);
    }


    //генерируем набор input-ов в соответствии с выбранным типом
    public function actionSubcat($modelId = null)
    {
        $id = Yii::$app->request->post('id');
        $characts = KindCharacteristicWork::find()->where(['kind_object_id' => $id])->orderBy(['characteristic_object_id' => SORT_ASC])->all();
        echo '<div style="border: 1px solid #D3D3D3; padding-left: 10px; padding-right: 10px; padding-bottom: 10px; margin-bottom: 20px; border-radius: 5px; width: 35%">';
        foreach ($characts as $c)
        {
            $value = ObjectCharacteristicWork::find()->where(['material_object_id' => $modelId])->andWhere(['characteristic_object_id' => $c->id])->one();
            $val = null;
            if ($value !== null)
            {
                if ($value->integer_value !== null) $val = $value->integer_value;
                if ($value->double_value !== null) $val = $value->double_value;
                if (strlen($value->string_value) > 0) $val = $value->string_value;
            }

            $type = "text";
            if ($c->characteristicObjectWork->value_type == 1 || $c->characteristicObjectWork->value_type == 2) $type = "number";
            echo '<div style="width: 50%; float: left; margin-top: 10px"><span>'.$c->characteristicObjectWork->name.': </span></div><div style="margin-top: 10px; margin-right: 0; min-width: 40%"><input type="'.$type.'" class="form-inline" style="border: 2px solid #D3D3D3; border-radius: 2px; min-width: 40%" name="MaterialObjectWork[characteristics][]" value="'.$val.'"></div>';
        }
        echo '</div>';
        exit;
        /*if ($operationPosts > 0) {
            $operations = AuditoriumWork::find()
                ->where(['branch_id' => $id])
                ->all();
            echo "<option value=null>" . "Вне отдела" . "</option>";
            foreach ($operations as $operation)
                echo "<option value='" . $operation->id . "'>" . $operation->name . ' (' . $operation->text . ')' . "</option>";
        } else
            echo "<option>-</option>";*/

    }

    /**
     * Deletes an existing MaterialObject model.
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
     * Finds the MaterialObject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return MaterialObject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = MaterialObjectWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
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

    public function actionGetFileCharacteristic($fileName = null, $modelObjId = null)
    {

        $type = 'characteristic';
        $filePath = '/upload/files/'.Yii::$app->controller->id;
        $filePath .= $type == null ? '/' : '/'.$type.'/';

        $downloadServ = new FileDownloadServer($filePath, $fileName);
        $downloadYadi = new FileDownloadYandexDisk($filePath, $fileName);

        $downloadServ->LoadFile();
        if (!$downloadServ->success) $downloadYadi->LoadFile();
        else return \Yii::$app->response->sendFile($downloadServ->file);

        if (!$downloadYadi->success) throw new \Exception('File not found');
        else {

            $fp = fopen('php://output', 'r');

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $downloadYadi->filename);
            header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . $downloadYadi->file->size);

            $downloadYadi->file->download($fp);

            fseek($fp, 0);
        }
    }

    public function actionDeleteFileCharacteristic($fileName = null, $modelCharId = null)
    {
        $model = ObjectCharacteristicWork::find()->where(['id' => $modelCharId])->one();
        unlink(Yii::$app->basePath . '/upload/files/material_object/characteristic/' . $fileName);
        $model->document_value = '';
        $model->save(false);
        //return $this->redirect('index?r=material/update&id='.$modelId);
    }
}
