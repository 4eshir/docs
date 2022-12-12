<?php

namespace app\controllers;

use Yii;
use app\models\common\Invoice;
use app\models\work\InvoiceWork;
use app\models\work\InvoiceEntryWork;
use app\models\work\EntryWork;
use app\models\work\MaterialObjectWork;
use app\models\work\SubobjectWork;
use app\models\work\MaterialObjectSubobjectWork;
use app\models\extended\MaterialObjectDynamic;
use app\models\work\KindCharacteristicWork;
use app\models\work\ObjectCharacteristicWork;
use app\models\extended\SubobjectWorkDuplicate;
use app\models\SearchInvoice;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use app\models\DynamicModel;

/**
 * InvoiceController implements the CRUD actions for Invoice model.
 */
class InvoiceController extends Controller
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
     * Lists all Invoice models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SearchInvoice();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Invoice model.
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
     * Creates a new Invoice model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new InvoiceWork();
        $modelObjects = [new MaterialObjectWork];

        if ($model->load(Yii::$app->request->post())) {
            $modelObjects = DynamicModel::createMultiple(MaterialObjectWork::classname());
            DynamicModel::loadMultiple($modelObjects, Yii::$app->request->post());
            $model->objects = $modelObjects;
            $model->documentFile = UploadedFile::getInstance($model, 'documentFile');

            $model->save(false);

            if ($model->documentFile !== null)
                $model->uploadDocument();

            $model->save(false);

            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelObjects' => $modelObjects,
        ]);
    }

    /**
     * Updates an existing Invoice model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $modelObjects = [new MaterialObjectWork];

        if ($model->load(Yii::$app->request->post())) {
            $modelObjects = DynamicModel::createMultiple(MaterialObjectWork::classname());
            DynamicModel::loadMultiple($modelObjects, Yii::$app->request->post());
            $model->objects = $modelObjects;
            $model->documentFile = UploadedFile::getInstance($model, 'documentFile');
            if ($model->documentFile !== null)
                $model->uploadDocument();

            $model->save(false);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelObjects' => $modelObjects,
        ]);
    }

    /**
     * Deletes an existing Invoice model.
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

    public function actionDeleteEntry($id, $modelId)
    {
        $invoiceEntry = InvoiceEntryWork::find()->where(['id' => $id])->one();
        $entries = EntryWork::find()->where(['id' => $invoiceEntry->entry_id])->all();
        $invoiceEntry->delete();

        foreach ($entries as $entry)
        {
            $tempId = $entry->object_id;
            $tempAmount = $entry->amount;
            $entry->delete();

            for ($i = $tempId; $i < $tempId + $tempAmount - 1; $i++)
            {
                $object = MaterialObjectWork::find()->where(['id' => $i])->one();
                $object->delete();
            }

        }

        return $this->redirect(['update', 'id' => $modelId]);
    }

    public function actionUpdateEntry($id, $modelId)
    {
        $model = EntryWork::find()->where(['id' => $id])->one();
        $model->fill();

        if ($model->load(Yii::$app->request->post()))
        {
            $model->dynamic = Yii::$app->request->post()["EntryWork"]["dynamic"];
            $model->save();
            return $this->redirect(['update', 'id' => $modelId]);
        }

        return $this->render('update-entry', [
            'model' => $model,
        ]);
    }

    public function actionUpdateObject($id)
    {
        $model = SubobjectWork::find()->where(['id' => $id])->one();
        $modelSubobject = [new SubobjectWorkDuplicate];

        if ($model->load(Yii::$app->request->post()))
        {
            $modelSubobject = DynamicModel::createMultiple(SubobjectWorkDuplicate::classname());
            DynamicModel::loadMultiple($modelSubobject, Yii::$app->request->post());
            $model->subobjects = $modelSubobject;

            $model->save(false);
            return $this->redirect(['update-object', 'id' => $model->id]);
        }

        return $this->render('update-object', [
            'model' => $model,
            'modelSubobject' => $modelSubobject,
        ]);
    }

    public function actionDeleteEntryDoc($id, $entryId, $modelId)
    {
        $file = ObjectCharacteristicWork::find()->where(['id' => $id])->one();
        $file->document_value = null;
        $file->save();
        //удалить физически файл!!!
        return $this->redirect('index?r=invoice/update-entry&id='.$entryId.'&modelId='.$modelId);
    }


    /**
     * Finds the Invoice model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Invoice the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InvoiceWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    protected function findObjectModelDynamic($id)
    {
        if (($model = Invoice::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDeleteObject($id, $modelId, $from = null)
    {
        $sub = SubobjectWork::find()->where(['id' => $id])->one();
        $subsubs = SubobjectWork::find()->where(['parent_id' => $id])->all();
        $mat_subs = MaterialObjectSubobjectWork::find()->where(['subobject_id' => $id])->all();

        if ($mat_subs !== null)
            foreach ($mat_subs as $one)
                $one->delete();

        if ($subsubs !== null)
            foreach ($subsubs as $one)
                $one->delete();

        $sub->delete();

        $invoiceId = InvoiceEntryWork::find()->where(['entry_id' => $modelId])->one()->invoice_id;

        if ($from == "entry")
            return $this->redirect('index?r=invoice/update-entry&id='.$modelId.'&modelId='.$invoiceId);
        else if ($from == "object")
            return $this->redirect('index?r=invoice/update-object&id='.$sub->parent_id.'&modelId='.$modelId);
        else
            return $this->redirect('index?r=invoice/update-entry&id='.$modelId.'&modelId='.$invoiceId);
    }

    //генерируем набор input-ов в соответствии с выбранным типом
    public function actionSubcat($modelId = null, $dmId = null)
    {
        $id = Yii::$app->request->post('id');
        $characts = KindCharacteristicWork::find()->where(['kind_object_id' => $id])->orderBy(['characteristic_object_id' => SORT_ASC])->all();
        echo '<div style="border: 1px solid #D3D3D3; padding-left: 10px; padding-right: 10px; padding-bottom: 10px; margin-bottom: 20px; border-radius: 5px; width: 200%" class="main-ch">';
        echo '<table>';
        $count = 0;
        foreach ($characts as $c)
        {
            $value = ObjectCharacteristicWork::find()->where(['material_object_id' => $modelId])->andWhere(['characteristic_object_id' => $c->id])->one();
            $val = null;
            if ($value !== null)
            {
                if ($value->integer_value !== null) $val = $value->integer_value;
                if ($value->double_value !== null) $val = $value->double_value;
                if (strlen($value->string_value) > 0) $val = $value->string_value;
                if ($value->bool_value !== null) $val = $value->bool_value;
                if ($value->date_value !== null) $val = $value->date_value;
                if (strlen($value->document_value) > 0) $val = $value->document_value;
            }

            $type = "text";
            if ($c->characteristicObjectWork->value_type == 1 || $c->characteristicObjectWork->value_type == 2) $type = "number";
            else if ($c->characteristicObjectWork->value_type == 4) $type = "checkbox";
            else if ($c->characteristicObjectWork->value_type == 5) $type = "date";
            else if ($c->characteristicObjectWork->value_type == 6) $type = "file";
            $placeholder = ['Введите число', 'Введите число', 'Введите текст'];

            echo '<tr><th style="width: 50%; float: left; margin-top: 10px;">'.$c->characteristicObjectWork->name.'</th>
                 <th style="float: left; margin-top: 10px; padding-left: 3%">';
            if ($type == "checkbox")
            {
                echo '<input type="'.$type.'" checked class="form-inline ch" name="MaterialObjectWork['.$count.'][characteristics][]" value="0" hidden>';
                if ($val == 1)
                    echo '<input onclick="handleClick(this)" type="'.$type.'" checked class="form-inline ch" name="CharacteristicInput"></th></tr>';
                else
                    echo '<input onclick="handleClick(this)" type="'.$type.'" class="form-inline ch" name="CharacteristicInput"></th></tr>';
            }
            else
                echo '<input step="any" type="'.$type.'" placeholder="'.$placeholder[$c->characteristicObjectWork->value_type-1].'" class="form-inline ch" name="MaterialObjectWork['.$count.'][characteristics][]" value="'.$val.'"></th></tr>';

            $count++;
        }
        echo '</table>';

        echo '</div>';
        exit;

    }


    public function actionGetFile($fileName = null, $modelId = null, $type = null)
    {
        $file = Yii::$app->basePath . '/upload/files/invoice/document/' . $type . '/' . $fileName;
        if (file_exists($file)) {
            return \Yii::$app->response->sendFile($file);
        }
        throw new \Exception('File not found');
        //return $this->redirect('index.php?r=docs-out/index');
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {

        $model = InvoiceWork::find()->where(['id' => $modelId])->one();
        unlink(Yii::$app->basePath . '/upload/files/invoice/document/' . $fileName);
        $model->document = '';
        $model->save(false);
        return $this->redirect('index?r=invoice/update&id='.$modelId);
    }
}
