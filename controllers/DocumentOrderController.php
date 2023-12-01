<?php

namespace app\controllers;

use app\models\components\ExcelWizard;
use app\models\components\RoleBaseAccess;
use app\models\components\WordWizard;
use app\models\extended\ForeignEventParticipantsExtended;
use app\models\strategies\FileDownloadStrategy\FileDownloadServer;
use app\models\strategies\FileDownloadStrategy\FileDownloadYandexDisk;
use app\models\work\BranchWork;
use app\models\work\DocumentOrderSupplementWork;
use app\models\work\ExpireWork;
use app\models\work\ForeignEventWork;
use app\models\work\NomenclatureWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\ParticipantFilesWork;
use app\models\work\RegulationWork;
use app\models\work\ResponsibleWork;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\DynamicModel;
use app\models\work\TeacherParticipantBranchWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TeamNameWork;
use app\models\work\TeamWork;
use app\models\work\TrainingGroupWork;
use Yii;
use app\models\work\DocumentOrderWork;
use app\models\SearchDocumentOrder;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use yii\widgets\ActiveForm;
use app\models\work\OrderErrorsWork;

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
    public function actionCreate($modelType = null)
    {
        $session = Yii::$app->session;
        $model = new DocumentOrderWork();
        $modelExpire = [new ExpireWork];
        $modelExpire2 = [new ExpireWork];
        $modelResponsible = [new ResponsibleWork];
        $modelParticipants = [new ForeignEventParticipantsExtended];

        if ($model->load(Yii::$app->request->post()) && $model->validate(false)) {
            $model->creator_id = Yii::$app->user->identity->getId();
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
            $modelParticipants = DynamicModel::createMultiple(ForeignEventParticipantsExtended::classname());
            DynamicModel::loadMultiple($modelParticipants, Yii::$app->request->post());
            $model->participants = $modelParticipants;

            if ($modelType == 2)
                $model->type = 2;

            if (true) {
                if ($model->archive_number === '' || $model->archive_number === NULL)
                    $model->getDocumentNumber();
                else
                {
                    $number = explode( '/',  $model->archive_number);
                    $model->order_number = $number[0];
                    $model->order_copy_id = $number[1];
                    if (count($number) > 2)
                        $model->order_postfix = $number[2];
                    if ($model->nomenclature_id === 5 || $model->nomenclature_id === NULL)
                        $model->type = 10;  // административный архивный
                    else
                        $model->type = 11;  // учебный архивный
                }

                if ($model->scanFile !== null)
                {
                    Logger::WriteLog(Yii::$app->user->identity->getId(),
                        'Добавлен скан к приказу ' . $model->order_name . ' ' . $model->order_number . '/' . $model->order_copy_id . (empty($model->order_postfix) ? '/' . $model->order_postfix : ''));
                    $model->uploadScanFile();
                }
                if ($model->docFiles != null)
                {
                    Logger::WriteLog(Yii::$app->user->identity->getId(),
                        'Добавлен редактируемый файл к приказу ' . $model->order_name . ' ' . $model->order_number . '/' . $model->order_copy_id . (empty($model->order_postfix) ? '/' . $model->order_postfix : ''));
                    $model->uploadDocFiles();
                }

                $model->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(),
                    'Создан приказ '.$model->order_name . ' ' . $model->order_number . '/' . $model->order_copy_id . (empty($model->order_postfix) ? '/' . $model->order_postfix : ''));
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelResponsible' => (empty($modelResponsible)) ? [new ResponsibleWork] : $modelResponsible,
            'modelExpire' => (empty($modelExpire)) ? [new ExpireWork] : $modelExpire,
            'modelExpire2' => (empty($modelExpire)) ? [new ExpireWork] : $modelExpire2,
            'modelParticipants' => (empty($modelParticipants)) ? [new ForeignEventParticipantsExtended] : $modelParticipants,
            'modelType' => $modelType,
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
        $model->type = 1;//$session->get('type') === '1' ? 1 : 0;
        $model->creator_id = Yii::$app->user->identity->getId();
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
    public function actionUpdate($id, $sideCall = null, $modelType = null)
    {
        $model = $this->findModel($id);
        $modelResponsible = DynamicModel::createMultiple(ResponsibleWork::classname());
        $modelExpire = DynamicModel::createMultiple(ExpireWork::classname());
        $modelParticipants = DynamicModel::createMultiple(ForeignEventParticipantsExtended::className());
        $modelType = $model->type;

        $supplement = DocumentOrderSupplementWork::find()->where(['document_order_id' => $id])->one();
        $model->supplement = $supplement;
        $foreign_event = ForeignEventWork::find()->where(['order_participation_id' => $id])->one();
        $model->foreign_event = $foreign_event;

        if ($model->type === 10 || $model->type === 11)
        {
            $model->archive_number = $model->order_number . '/' . $model->order_copy_id;
            if ($model->order_postfix !== null)
                $model->archive_number .= '/' . $model->order_postfix;
        }
        DynamicModel::loadMultiple($modelResponsible, Yii::$app->request->post());
        $model->responsibles = $modelResponsible;
        if ($model->load(Yii::$app->request->post()))
        {
            $model->scanFile = UploadedFile::getInstance($model, 'scanFile');
            $model->docFiles = UploadedFile::getInstances($model, 'docFiles');
            $modelResponsible = DynamicModel::createMultiple(ResponsibleWork::classname());
            DynamicModel::loadMultiple($modelResponsible, Yii::$app->request->post());
            $model->responsibles = $modelResponsible;
            $modelExpire = DynamicModel::createMultiple(ExpireWork::classname());
            DynamicModel::loadMultiple($modelExpire, Yii::$app->request->post());
            $model->expires = $modelExpire;
            $modelParticipants = DynamicModel::createMultiple(ForeignEventParticipantsExtended::classname());
            DynamicModel::loadMultiple($modelParticipants, Yii::$app->request->post());
            $model->participants = $modelParticipants;

            if ($model->validate(false))
            {
                $cur = DocumentOrderWork::find()->where(['id' => $model->id])->one();

                if ($model->archive_number == "")
                {
                    if ($cur->order_date !== $model->order_date)
                        $model->getDocumentNumber();
                }
                else
                {
                    $number = explode( '/',  $model->archive_number);
                    $model->order_number = $number[0];
                    $model->order_copy_id = $number[1];
                    if (count($number) > 2)
                        $model->order_postfix = $number[2];
                    //$model->order_copy_id = $model->archive_number;
                    if ($model->nomenclature_id === 5 || $model->nomenclature_id === NULL)
                        $model->type = 10;  // административный архивный
                    else
                        $model->type = 11;  // учебный архивный
                }
                if ($model->scanFile !== null)
                    $model->uploadScanFile();
                if ($model->docFiles != null)
                    $model->uploadDocFiles(10);

                $i = 0;
                foreach ($modelParticipants as $modelParticipantOne)
                {
                    if (strlen($modelParticipantOne->file) == 0)
                    {
                        $modelParticipantOne->file = \yii\web\UploadedFile::getInstance($modelParticipantOne, "[{$i}]file");
                        if ($modelParticipantOne->file !== null) $modelParticipantOne->uploadFile($model->foreign_event["name"], $model->foreign_event["start_date"]);
                    }
                    else
                    {
                        $modelParticipantOne->uploadCopyFile($modelParticipantOne->file);
                    }
                    $i++;
                }

                $model->save(false);
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменен приказ '.$model->order_name.' '.$model->order_number.'/'.$model->order_postfix. (empty($model->order_postfix) ? '/'.$model->order_postfix : ''));
                return $this->redirect(['view', 'id' => $model->id]);
            }
            if ($sideCall === null)
                return $this->redirect(['view', 'id' => $model->id]);
            else
                return $this->render('update', [
                    'model' => $model,
                    'modelResponsible' => (empty($modelResponsible)) ? [new ResponsibleWork] : $modelResponsible,
                    'modelExpire' => (empty($modelExpire)) ? [new ExpireWork] : $modelExpire,
                    'modelParticipants' => (empty($modelParticipants)) ? [new ForeignEventParticipantsExtended] : $modelParticipants,
                    'modelType' => $modelType,
                ]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelResponsible' => (empty($modelResponsible)) ? [new ResponsibleWork] : $modelResponsible,
            'modelExpire' => (empty($modelExpire)) ? [new ExpireWork] : $modelExpire,
            'modelParticipants' => (empty($modelParticipants)) ? [new ForeignEventParticipantsExtended] : $modelParticipants,
            'modelType' => $modelType,
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
        $model->groups_check = ['nope'];
        $model->participants_check = ['nope'];
        $model->new_groups_check = ['nope'];
        if ($type == 'scan')
        {
            $model->scan = '';
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален скан-файл ' . $model->scan . ' из приказа ' .$model->order_name . ' ' . $model->order_number.'/'.$model->order_postfix);
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
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален редактируемый файл ' . $deleteFile . ' из приказа ' .$model->order_name . ' ' . $model->order_number.'/'.$model->order_postfix);
        }
        return $this->redirect('index?r=document-order/update&id='.$model->id);
    }

    public function actionGetFile($fileName = null, $event = null, $type = null)
    {
        $filePath = '/upload/files/'.Yii::$app->controller->id;
        if ($event == true)
            $filePath = '/upload/files/foreign-event';
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

    public function actionDeleteResponsible($peopleId, $orderId)
    {
        $resp = ResponsibleWork::find()->where(['people_id' => $peopleId])->andWhere(['document_order_id' => $orderId])->one();
        if ($resp != null)
            $resp->delete();

        $model = $this->findModel($orderId);
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален ответственный (id='.$peopleId.') из приказа '.$model->order_name.' '.$model->order_number.'/'.$model->order_postfix. (empty($model->order_postfix) ? '/'.$model->order_postfix : ''));
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
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален приказ (id='.$order->id.') '.$name . ' № ' . $order->order_number . '/' . $order->order_copy_id . (empty($model->order_postfix) ? '/' . $order->order_postfix : ''));
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
        $date = Yii::$app->request->post('date');
        if ($id = Yii::$app->request->post('id')) {
            $operationPosts = BranchWork::find()
                ->where(['id' => $id])
                ->count();

            if ($operationPosts > 0) {
                $operations = NomenclatureWork::find()
                    ->where(['branch_id' => $id])
                    ->andWhere(['actuality' => 0])
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
                    echo '<input type="checkbox" checked="true" id="documentorderwork-groups_check" name="DocumentOrderWork[groups_check][]" onchange="displayParticipant()" value="'.$group->id.'">';
                else
                    echo '<input type="checkbox" id="documentorderwork-groups_check" name="DocumentOrderWork[groups_check][]" onchange="displayParticipant()" value="'.$group->id.'">';
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

            echo '</tbody></table></div>';//.'|split|';

            /*----------------------*/

            echo '<br><b>Учащиеся учебных групп: </b>';
            echo '<input type="text" id="participantSearch" onkeydown="return preventEnter(event.key)" onchange="searchParticipant()" placeholder="Поиск по учащимся..." title="Введите имя">';
            echo '<div style="max-height: 400px; overflow-y: scroll; margin-top: 1em;"><table id="order_participant" class="table table-bordered"><thead><tr><th><input type="checkbox" id="checker0" onclick="allCheck()"></th><th><a onclick="sortParticipant(1)"><b>Учащийся</b></a></th><th><a onclick="sortParticipant(1)"><b>Текущая учебная группа</b></a></th><th style="display: none;"><b>Новая учебная группа</b></th></tr></thead>';
            echo '';
            echo '<tbody>';
            $groupParticipants = \app\models\work\TrainingGroupParticipantWork::find()/*->where(['status' => 0])*/->andWhere(['IN', 'training_group_id',
                (new Query())->select('id')->from('training_group')->where(['order_stop' => 0])->andWhere(['archive' => 0])->andWhere(['branch_id' => $id])])->all();//->orderBy('training_group_id')->all();
            $part = \app\models\work\ForeignEventParticipantsWork::find();
            $stud = \app\models\work\TrainingGroupWork::find();
            foreach ($groupParticipants as $groupParticipant) {
                $ordersParticipant = \app\models\work\OrderGroupParticipantWork::find()->where(['group_participant_id' => $groupParticipant->id])->andWhere(['link_id' => NULL])->andWhere(['IN', 'order_group_id',
                    (new Query())->select('id')->from('order_group')->where(['document_order_id' => $idG])])->all();
                if ($groups[0]->CheckParticipantStatus($groupParticipant) == 0 || count($ordersParticipant) !== 0)
                {
                    echo '<tr><td style="width: 10px">';
                    if (count($ordersParticipant) !== 0)
                        echo '<input type="checkbox" checked="true" id="documentorderwork-participants_check" name="DocumentOrderWork[participants_check][]" class="check" value="' . $groupParticipant->id . '">';
                    else
                        echo '<input type="checkbox" id="documentorderwork-participants_check" name="DocumentOrderWork[participants_check][]" class="check" value="' . $groupParticipant->id . '">';
                    echo '</td><td style="width: auto">';
                    echo $part->where(['id' => $groupParticipant->participant_id])->one()->getFullName();
                    echo '</td><td style="width: auto">';
                    $gr = $stud->where(['id' => $groupParticipant->training_group_id])->one();
                    echo $gr->number;
                    //else // тут выпадающий список групп, но если нет основной группы, то всё остальное скрывается js
                    //{
                    $text = '</td><td style="width: auto; display: none"><div class="form-group field-documentorderwork-new_groups_check">'
                        . '<select id="documentorderwork-new_groups_check" class="form-control" name="DocumentOrderWork[new_groups_check][' . $groupParticipant->id . '][' . $groupParticipant->participant_id . '][]">';
                    echo $text;
                    //$newGroups = $stud->where(['training_program_id' => $gr->training_program_id])->andWhere(['!=', 'id', $gr->id])->andWhere(['>', 'finish_date', $date])->all();
                    $newGroups = $stud->where(['!=', 'id', $gr->id])->andWhere(['>=', 'finish_date', $date])->andWhere(['branch_id' => $gr->branch_id])->andWhere(['archive' => 0])->all();
                    if (count($newGroups) > 0) {
                        foreach ($newGroups as $newGroup)
                            echo "<option value='" . $newGroup->id . "'>" . $newGroup->number . "</option>";
                    } else
                        echo "<option>-</option>";
                    //-----
                    echo '</select></div></td></tr>';
                }
            }
            echo '</tbody></table></div>'.'|split|';
            //echo '<br>';
            //echo '<div id="study-type"><div class="form-group field-study_type-0"><input type="hidden" name="DocumentOrderWork[study_type]" value="0"><label><input type="checkbox" id="study_type-0" name="DocumentOrderWork[study_type]" value=""> По заявлению родителя или законного представителя</label></div></div>'.'|split|';
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

        throw new NotFoundHttpException('Запрошенная страница не существует(');
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
    public function actionAmnesty ($id)
    {
        $model = $this->findModel($id);

        $errorsAmnesty = new OrderErrorsWork();
        $errorsAmnesty->OrderAmnesty($id);

        Logger::WriteLog(Yii::$app->user->identity->getId(),
            'Прощены ошибки в приказе '.$model->order_name . ' ' . $model->order_number . '/' . $model->order_copy_id . (empty($model->order_postfix) ? '/' . $model->order_postfix : ''));

        return $this->redirect('index?r=document-order/view&id='.$id);
    }

    // Новый функционал - генерация образовательных приказов и об участии в (основной приказ)
    public function actionGenerationWord($order_id, $type)
    {
        $model = $this->findModel($order_id);
        $checkGenerate = new OrderErrorsWork();

        switch ($type) {
            case -1: $checkGenerate->PermissionToParticipate($order_id) ? WordWizard::ParticipationEvent($order_id) : Yii::$app->session->addFlash('warning', 'Исправьте ошибки заполнения, чтобы сгенерировать приказ'); break;
            case 0: WordWizard::Enrolment($order_id);
            case 1: WordWizard::Deduction($order_id);
            case 2: WordWizard::Transfer($order_id);
        }

        Logger::WriteLog(Yii::$app->user->identity->getId(),
            'Сгенерирован и выгружен файл приказа '.$model->order_name . ' ' . $model->order_number . '/' . $model->order_copy_id . (empty($model->order_postfix) ? '/' . $model->order_postfix : ''));

        return $this->render('view', [
            'model' => $this->findModel($order_id),
        ]);
    }

    public function actionGenerationProtocol($order_id)
    {
        $model = $this->findModel($order_id);
        WordWizard::ProtocolCommission($order_id);

        Logger::WriteLog(Yii::$app->user->identity->getId(),
            'Сгенерирован и выгружен протокол аттестационной комисии к приказу '.$model->order_name . ' ' . $model->order_number . '/' . $model->order_copy_id . (empty($model->order_postfix) ? '/' . $model->order_postfix : ''));

        return $this->render('view', [
            'model' => $this->findModel($order_id),
        ]);
    }

    public function actionUpdateParticipant($id, $model_id)
    {
        $model = TeacherParticipantWork::find()->where(['id' => $id])->one();
        $model->getTeam();
        $model->branchs = $model->getBranchs();
        $back = 'order';
        if ($model->load(Yii::$app->request->post()))
        {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file !== null)
                $model->uploadParticipantFiles();
            $model->save(false);

            return $this->redirect('index.php?r=document-order/update&id='.$model_id);
        }

        return $this->render('../foreign-event/update-participant',[
            'model' => $model,
            'back' => $back,
        ]);
    }

    public function actionDeleteParticipant($id, $model_id)
    {
        $part = TeacherParticipantWork::find()->where(['id' => $id])->one();
        $achivment = ParticipantAchievementWork::find()->where(['teacher_participant_id' => $id])->one();

        if ($achivment !== null)
            Yii::$app->session->addFlash('warning', 'Невозможно удалить запись участия, если у участника есть достижения');
        else
        {
            $team = TeamWork::find()->where(['teacher_participant_id' => $id])->one();
            if ($team !== null)
                $team->delete();

            $branchs = TeacherParticipantBranchWork::find()->where(['teacher_participant_id' => $id])->all();
            foreach ($branchs as $branch) $branch->delete();

            $file = ParticipantFilesWork::find()->where(['teacher_participant_id' => $id])->one();
            if ($file !== null)
                $file->delete();

            $part->delete();
        }

        return $this->redirect('index.php?r=document-order/update&id='.$model_id);
    }
}
