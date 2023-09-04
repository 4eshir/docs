<?php

namespace app\controllers;

use app\models\DynamicModel;
use app\models\work\CharacteristicObjectWork;
use app\models\work\DropdownCharacteristicObjectWork;
use app\models\work\KindCharacteristicWork;
use app\models\work\KindObjectWork;
use app\models\work\ObjectCharacteristicWork;
use app\models\work\TrainingGroupExpertWork;
use Yii;
use app\models\common\KindObject;
use app\models\SearchKindObject;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * KindObjectController implements the CRUD actions for KindObject model.
 */
class KindObjectController extends Controller
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
     * Lists all KindObject models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchKindObject();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single KindObject model.
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
     * Creates a new KindObject model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new KindObjectWork();
        $modelCharacteristics = [new CharacteristicObjectWork];

        if ($model->load(Yii::$app->request->post())) {
            $modelCharacteristics = DynamicModel::createMultiple(CharacteristicObjectWork::classname());
            DynamicModel::loadMultiple($modelCharacteristics, Yii::$app->request->post());
            $model->chars = $modelCharacteristics;
            $model->save();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelCharacteristics' => $modelCharacteristics,
        ]);
    }

    /**
     * Updates an existing KindObject model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelCharacteristics = [new CharacteristicObjectWork];

        if ($model->load(Yii::$app->request->post())) {
            $modelCharacteristics = DynamicModel::createMultiple(CharacteristicObjectWork::classname());
            DynamicModel::loadMultiple($modelCharacteristics, Yii::$app->request->post());
            $model->chars = $modelCharacteristics;
            $model->save();

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelCharacteristics' => $modelCharacteristics,
        ]);
    }

    /**
     * Deletes an existing KindObject model.
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

    public function actionDeleteCharacteristic($id, $modelId)
    {
        $charKind = KindCharacteristicWork::find()->where(['id' => $id])->one();

        $stopList = ObjectCharacteristicWork::find()->where(['characteristic_object_id' => $charKind->characteristic_object_id])->all();
        if (count($stopList) > 0)
        {
            Yii::$app->session->setFlash("danger", "Невозможно удалить характеристику, т.к. имеются связанные с ней данные в объектах");
            return $this->redirect('index?r=kind-object/update&id='.$modelId);
        }
        else
        {
            $char = CharacteristicObjectWork::find()->where(['id' => $charKind->characteristic_object_id])->one();
            $dd = DropdownCharacteristicObjectWork::find()->where(['characteristic_object_id' => $charKind->characteristic_object_id])->all();

            if ($dd !== null)
                foreach ($dd as $item)
                    $item->delete();

            $charKind->delete();
            $char->delete();
        }

        return $this->redirect('index?r=kind-object/update&id='.$modelId);
    }

    /**
     * Finds the KindObject model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return KindObject the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = KindObjectWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
