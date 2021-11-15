<?php

namespace app\controllers;

use app\models\components\RoleBaseAccess;
use app\models\work\BranchWork;
use app\models\work\ExpireWork;
use app\models\work\NomenclatureWork;
use app\models\work\RegulationWork;
use app\models\work\ResponsibleWork;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\DynamicModel;
use Yii;
use app\models\work\DocumentOrderWork;
use app\models\SearchDocumentOrder;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;

/**
 * DocumentOrderController implements the CRUD actions for DocumentOrder model.
 */
class DocumentOrderController extends Controller
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
     * Lists all DocumentOrder models.
     * @return mixed
     */
    public function actionIndex($c = null)
    {
        $session = Yii::$app->session;
        $session->set('type', $c);
        $searchModel = new SearchDocumentOrder();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, $c);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DocumentOrder model.
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
     * Creates a new DocumentOrder model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $session = Yii::$app->session;
        $model = new DocumentOrderWork();
        //$model->order_number = NomenclatureWork::find()->where([]);
        $modelExpire = [new ExpireWork];
        $modelExpire2 = [new ExpireWork];
        $modelResponsible = [new ResponsibleWork];
        if ($model->load(Yii::$app->request->post())) {
            $model->signed_id = null;
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
            $model->scan = '';
            $model->state = true;

            $modelResponsible = DynamicModel::createMultiple(ResponsibleWork::classname());
            DynamicModel::loadMultiple($modelResponsible, Yii::$app->request->post());
            $model->responsibles = $modelResponsible;
            $modelExpire = DynamicModel::createMultiple(ExpireWork::classname());
            DynamicModel::loadMultiple($modelExpire, Yii::$app->request->post());
            $model->expires = $modelExpire;

            if ($model->validate(false)) {
                if ($model->archive_number === '')
                    $model->getDocumentNumber();
                else
                {
                    $number = explode( '/',  $model->archive_number);
                    $model->order_number = $number[0];
                    $model->order_copy_id = $number[1];
                    if (count($number) > 2)
                        $model->order_postfix = $number[2];
                    //$model->order_copy_id = $model->archive_number;
                    $model->type = 10;
                }

                if ($model->scanFile !== null)
                    $model->uploadScanFile();
                if ($model->docFiles != null)
                    $model->uploadDocFiles();
                
                $model->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменен приказ '.$model->order_name);
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelResponsible' => (empty($modelResponsible)) ? [new ResponsibleWork] : $modelResponsible,
            'modelExpire' => (empty($modelExpire)) ? [new ExpireWork] : $modelExpire,
            'modelExpire2' => (empty($modelExpire)) ? [new ExpireWork] : $modelExpire2,
        ]);
    }

    public function actionCreateReserve()
    {
        if (!RoleBaseAccess::CheckAccess('document-order', 'create-reserve', Yii::$app->user->identity->getId(), $_GET['c'] === '1' ? 2 : 1)) {
            return $this->redirect(['/site/error-access']);
        }
        $model = new DocumentOrderWork();
        $session = Yii::$app->session;
        $model->order_name = 'Резерв';
        $model->order_number = '02-02';
        $model->order_date = date("Y-m-d");
        $model->scan = '';
        $model->state = true;
        $model->type = $session->get('type') === '1' ? 1 : 0;
        $model->register_id = Yii::$app->user->identity->getId();
        $model->getDocumentNumber();
        Yii::$app->session->addFlash('success', 'Резерв успешно добавлен');
        $model->save(false);
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлен резерв приказа '.$model->order_number.'/'.$model->order_postfix);
        return $this->redirect('index.php?r=document-order/index&c='.$session->get('type'));
    }

    /**
     * Updates an existing DocumentOrder model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id, $sideCall = null)
    {
        $model = $this->findModel($id);
        $modelResponsible = DynamicModel::createMultiple(ResponsibleWork::classname());
        $modelExpire = DynamicModel::createMultiple(ExpireWork::classname());
        if ($model->type === 10)
        {
            $model->archive_number = $model->order_number . '/' . $model->order_copy_id;
            if ($model->order_postfix !== null)
                $model->archive_number .= '/' . $model->order_postfix;
        }
        DynamicModel::loadMultiple($modelResponsible, Yii::$app->request->post());
        $model->responsibles = $modelResponsible;
        if ($model->load(Yii::$app->request->post())) {
            //var_dump('kek');
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
            $modelResponsible = DynamicModel::createMultiple(ResponsibleWork::classname());
            DynamicModel::loadMultiple($modelResponsible, Yii::$app->request->post());
            $model->responsibles = $modelResponsible;
            $modelExpire = DynamicModel::createMultiple(ExpireWork::classname());
            DynamicModel::loadMultiple($modelExpire, Yii::$app->request->post());
            $model->expires = $modelExpire;

            if ($model->validate(false)) {
                $cur = DocumentOrderWork::find()->where(['id' => $model->id])->one();
                if ($model->archive_number !== "")
                    if ($cur->order_number !== $model->order_number)
                        $model->getDocumentNumber();
                else
                {
                    $number = explode( '/',  $model->archive_number);
                    $model->order_number = $number[0];
                    $model->order_copy_id = $number[1];
                    if (count($number) > 2)
                        $model->order_postfix = $number[2];
                    //$model->order_copy_id = $model->archive_number;
                    $model->type = 10;
                }
                if ($model->scanFile !== null)
                    $model->uploadScanFile();
                if ($model->docFiles != null)
                    $model->uploadDocFiles(10);

                $model->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменен приказ '.$model->order_name);
            }
            if ($sideCall === null)
                return $this->redirect(['view', 'id' => $model->id]);
            else
                return $this->render('update', [
                    'model' => $model,
                    'modelResponsible' => (empty($modelResponsible)) ? [new ResponsibleWork] : $modelResponsible,
                    'modelExpire' => (empty($modelExpire)) ? [new ExpireWork] : $modelExpire,
                ]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelResponsible' => (empty($modelResponsible)) ? [new ResponsibleWork] : $modelResponsible,
            'modelExpire' => (empty($modelExpire)) ? [new ExpireWork] : $modelExpire,
        ]);
    }

    public function actionDeleteExpire($expireId, $modelId)
    {
        $expire = ExpireWork::find()->where(['id' => $expireId])->one();
        $order = DocumentOrderWork::find()->where(['id' => $expire->expire_order_id])->one();
        if ($order !== null)
        {
            $order->state = 1;
            RegulationWork::CheckRegulationState($order->id, 1);
            $order->save(false);
            $model = DocumentOrderWork::find()->where(['id' => $modelId])->one();

        }
        $reg = RegulationWork::find()->where(['id' => $expire->expire_regulation_id])->one();
        if ($reg !== null)
        {
            $reg->state = 'Утратило силу';
            $reg->save(false);
        }
        $expire->delete();

        $model = DocumentOrderWork::find()->where(['id' => $modelId])->one();
        return $this->actionUpdate($modelId, 1);
        /*return $this->render('update', [
            'model' => $model,
            'modelResponsible' => (empty($modelResponsible)) ? [new ResponsibleWork] : $modelResponsible,
            'modelExpire' => (empty($modelExpire)) ? [new ExpireWork] : $modelExpire
        ]);*/
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {
        $model = DocumentOrderWork::find()->where(['id' => $modelId])->one();

        if ($type == 'scan')
        {
            $model->scan = '';
            $model->save(false);
            return $this->redirect('index?r=document-order/update&id='.$model->id);
        }


        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null) {

            $result = '';
            $split = explode(" ", $model->doc);
            $deleteFile = '';
            for ($i = 0; $i < count($split) - 1; $i++) {
                if ($split[$i] !== $fileName) {
                    $result = $result . $split[$i] . ' ';
                } else
                    $deleteFile = $split[$i];
            }
            $model->doc = $result;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален файл ' . $deleteFile);
        }
        return $this->redirect('index?r=document-order/update&id='.$model->id);
    }

    public function actionGetFile($fileName = null, $modelId = null, $type = null)
    {
        $file = Yii::$app->basePath . '/upload/files/order/' . $type . '/' . $fileName;
        if (file_exists($file)) {
            return \Yii::$app->response->sendFile($file);
        }
        throw new \Exception('File not found');
        //return $this->redirect('index.php?r=docs-out/index');
    }

    public function actionDeleteResponsible($peopleId, $orderId)
    {
        $resp = ResponsibleWork::find()->where(['people_id' => $peopleId])->andWhere(['document_order_id' => $orderId])->one();
        if ($resp != null)
            $resp->delete();

        return $this->actionUpdate($orderId, 1);

        //return $this->redirect('index.php?r=document-order/update&id='.$orderId);
    }

    /**
     * Deletes an existing DocumentOrder model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $order = $this->findModel($id);
        $name = $order->order_name;
        if (!$order->checkForeignKeys())
        {
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален приказ '.$name);
            $order->delete();
            Yii::$app->session->addFlash('success', 'Приказ "' . $name . '" успешно удален');
        }
        else
            Yii::$app->session->addFlash('error', 'Приказ "' . $name . '" невозможно удалить. Он упоминается в одном или нескольких положениях!');

        return $this->redirect(['index']);
    }

    public function actionSubattr()
    {
        $idG = Yii::$app->request->post('idG');
        if ($id = Yii::$app->request->post('id')) {
            $operationPosts = BranchWork::find()
                ->where(['id' => $id])
                ->count();

            if ($operationPosts > 0) {
                $operations = NomenclatureWork::find()
                    ->where(['branch_id' => $id])
                    ->all();
                foreach ($operations as $operation)
                    echo "<option value='" . $operation->number . "'>" . $operation->fullNameWork . "</option>";
            } else
                echo "<option>-</option>";
            echo '|split|';

            echo '<b>Фильтры для учебных групп: </b>';
            echo '<input type="text" id="nameSearch" onchange="searchColumn()" placeholder="Поиск по части имени..." title="Введите имя">';
            echo '    С <input type="date" id="nameLeftDate" onchange="searchColumn()" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="Поиск по дате начала занятий...">';
            echo '    По <input type="date" id="nameRightDate" onchange="searchColumn()" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="Поиск по дате начала занятий...">';

            echo '<div style="max-height: 400px; overflow-y: scroll; margin-top: 1em;"><table id="sortable" class="table table-bordered"><thead><tr><th></th><th><a onclick="sortColumn(1)"><b>Учебная группа</b></a></th><th><a onclick="sortColumn(2)"><b>Дата начала занятий</b></a></th><th><a onclick="sortColumn(3)"><b>Дата окончания занятий</b></a></th></tr></thead>';
            echo '';
            echo '<tbody>';
            $groups = \app\models\work\TrainingGroupWork::find()->where(['order_stop' => 0])->andWhere(['archive' => 0])->andWhere(['branch_id' => $id])->all();
            foreach ($groups as $group)
            {
                $orders = \app\models\work\OrderGroupWork::find()->where(['training_group_id' => $group->id])->andWhere(['document_order_id' => $idG])->one();
                echo '<tr><td style="width: 10px">';
                if ($orders !== null)
                    echo '<input type="checkbox" checked="true" id="documentorderwork-groups_check" name="DocumentOrderWork[groups_check][]" value="'.$group->id.'">';
                else
                    echo '<input type="checkbox" id="documentorderwork-groups_check" name="DocumentOrderWork[groups_check][]" value="'.$group->id.'">';
                echo '</td><td style="width: auto">';
                echo $group->number;
                echo '</td>';
                echo '</td><td style="width: auto">';
                echo $group->start_date;
                echo '</td>';
                echo '</td><td style="width: auto">';
                echo $group->finish_date;
                echo '</td></tr>';
            }

            echo '</tbody></table></div>'.'|split|';
        }
    }

    /**
     * Finds the DocumentOrder model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DocumentOrderWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DocumentOrderWork::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }

    //Проверка на права доступа к CRUD-операциям
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        $session = Yii::$app->session;
        $c = $_GET['c'];
        if ($_GET['c']  === null) $c = $session->get('type');
        if (!RoleBaseAccess::CheckAccess($action->controller->id, $action->id, Yii::$app->user->identity->getId(), $c == '1' ? 1 : 2)) {
            $this->redirect(['/site/error-access']);
            return false;
        }
        return parent::beforeAction($action);
    }
}
