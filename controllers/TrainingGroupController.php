<?php

namespace app\controllers;

use app\models\components\PdfWizard;
use app\models\components\RoleBaseAccess;
use app\models\strategies\FileDownloadStrategy\FileDownloadServer;
use app\models\strategies\FileDownloadStrategy\FileDownloadYandexDisk;
use app\models\work\AccessLevelWork;
use app\models\work\AuditoriumWork;
use app\models\work\BranchWork;
use app\models\work\CertificatWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\GroupErrorsWork;
use app\models\work\LessonThemeWork;
use app\models\work\NomenclatureWork;
use app\models\work\ExperWork;
use app\models\work\GroupProjectThemesWork;
use app\models\work\OrderGroupWork;
use app\models\work\PeopleWork;
use app\models\work\PersonalDataTrainingParticipantGroupWork;
use app\models\work\TeacherGroupWork;
use app\models\work\OrderGroupParticipantWork;
use app\models\work\ThematicPlanWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use app\models\work\TrainingProgramWork;
use app\models\work\VisitWork;
use app\models\work\TrainingGroupExpertWork;
use app\models\components\ExcelWizard;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\DynamicModel;
use app\models\extended\AccessTrainingGroup;
use app\models\extended\TrainingGroupAuto;
use PHPExcel_Shared_Date;
use stdClass;
use Yii;
use app\models\SearchTrainingGroup;
use yii\helpers\FileHelper;
use yii\helpers\Json;
use yii\helpers\VarDumper;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\web\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * TrainingGroupController implements the CRUD actions for TrainingGroup model.
 */
class TrainingGroupController extends Controller
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
     * Lists all TrainingGroup models.
     * @return mixed
     */

    public function actionIndex($archive = null)
    {
        $searchModel = new SearchTrainingGroup();

        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionArchive($arch, $unarch)
    {
        $err = new GroupErrorsWork();
        $arch = explode(',', $arch);
        $unarch = explode(',', $unarch);
        $status = "<br><br>";
        if ($arch[0] !== "")
        {
            for ($i = 0; $i < count($arch); $i++)
            {
                $group = TrainingGroupWork::find()->where(['id' => $arch[$i]])->one();

                $errors = GroupErrorsWork::find()->where(['training_group_id' => $arch[$i]])->andWhere(['time_the_end' => null])->andWhere(['amnesty' => null])->andWhere(['!=', 'errors_id', 21])->all();
                if (count($errors) > 0)
                    $status .= 'Учебная группа ' . $group->number . ' содержит ошибки и не может быть отправлена в архив <br>';
                else {
                    $group->archive = 1;
                    $group->save();
                    Logger::WriteLog(Yii::$app->user->identity->getId(), 'Группа ['.$group->id.'] отправлена в архив');
                    $err->CheckArchiveTrainingGroup($arch[$i]);
                }
            }
        }
        
        if ($unarch[0] !== "")
        {
            for ($i = 0; $i < count($unarch); $i++)
            {
                $group = TrainingGroupWork::find()->where(['id' => $unarch[$i]])->one();
                $group->archive = 0;
                $group->save();
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Группа ['.$group->id.'] разархивирована');
            }
        }
        
        Yii::$app->session->setFlash("success", 'Изменение статуса групп произведено успешно' . $status);
        return $this->redirect(['/training-group/index']);
        /*
        $searchModel = new SearchTrainingGroup();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $dataProvider->pagination = false;

        $realIds = [];
        for ($i = ($p - 1) * 20; $i < count($dataProvider->keys) && $i < $p * 20; $i++) $realIds[] = $dataProvider->keys[$i];


        $selections = explode(',', $ids);
        $flashStr = "";
        $allGroups = TrainingGroupWork::find()->where(['IN', 'id', $realIds])->all();
        $errors = new GroupErrorsWork();
        foreach ($allGroups as $group) {
            $group->archive = 0;
            $group->save();
        }
        if ($ids !== "")
            for ($i = 0; $i < count($selections); $i++)
            {
                $tag = TrainingGroupWork::findOne($selections[$i]);
                $tag->archive === 1 ? $tag->archive = 0 : $tag->archive = 1;
                $tag->save(false);
                if ($tag->archive === 0)
                    $flashStr .= "Группа ".$tag->number." разархивирована\n";
                else
                    $flashStr .= "Группа ".$tag->number." архивирована\n";

                $errors->CheckArchiveTrainingGroup($tag->id);
            }
        Yii::$app->session->setFlash("success", 'Изменение статуса групп произведено успешно');
        return $this->redirect(['/training-group/index']);
        */
    }

    /**
     * Displays a single TrainingGroup model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);
        //if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), Yii::$app->controller->action->id, Yii::$app->controller->id) && !AccessTrainingGroup::CheckAccess(Yii::$app->user->identity->getId(), $id)) {
        //    return $this->render('/site/error');
        //}
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new TrainingGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {

        $session = Yii::$app->session;
        if ($session->get("show") === null)
            $session->set("show", "common");

        $model = new TrainingGroupWork();
        $modelTrainingGroupParticipant = [new TrainingGroupParticipantWork];
        $modelTrainingGroupLesson = [new TrainingGroupLessonWork];
        $modelTrainingGroupAuto = [new TrainingGroupAuto];
        $modelOrderGroup = [new OrderGroupWork];
        $modelTeachers = [new TeacherGroupWork];
        $modelProjectThemes = [new GroupProjectThemesWork];
        $modelExperts = [new TrainingGroupExpertWork];

        if ($model->load(Yii::$app->request->post())) {


            $model->number = "";
            $model->photosFile = UploadedFile::getInstances($model, 'photosFile');
            $model->presentDataFile = UploadedFile::getInstances($model, 'presentDataFile');
            $model->workDataFile = UploadedFile::getInstances($model, 'workDataFile');
            $modelTrainingGroupParticipant = DynamicModel::createMultiple(TrainingGroupParticipantWork::classname());
            DynamicModel::loadMultiple($modelTrainingGroupParticipant, Yii::$app->request->post());
            $model->participants = $modelTrainingGroupParticipant;
            $modelTrainingGroupLesson = DynamicModel::createMultiple(TrainingGroupLessonWork::classname());
            DynamicModel::loadMultiple($modelTrainingGroupLesson, Yii::$app->request->post());
            $model->lessons = $modelTrainingGroupLesson;
            $modelTrainingGroupAuto = DynamicModel::createMultiple(TrainingGroupAuto::classname());
            DynamicModel::loadMultiple($modelTrainingGroupAuto, Yii::$app->request->post());
            $model->auto = $modelTrainingGroupAuto;
            $modelOrderGroup = DynamicModel::createMultiple(OrderGroupWork::classname());
            DynamicModel::loadMultiple($modelOrderGroup, Yii::$app->request->post());
            $model->orders = $modelOrderGroup;
            $modelProjectThemes = DynamicModel::createMultiple(GroupProjectThemesWork::classname());
            DynamicModel::loadMultiple($modelProjectThemes, Yii::$app->request->post());
            $model->themes = $modelProjectThemes;
            $modelExperts = DynamicModel::createMultiple(TrainingGroupExpertWork::classname());
            DynamicModel::loadMultiple($modelExperts, Yii::$app->request->post());
            $model->experts = $modelExperts;

            $modelTeachers = DynamicModel::createMultiple(TeacherGroupWork::classname());
            DynamicModel::loadMultiple($modelTeachers, Yii::$app->request->post());
            $model->teachers = $modelTeachers;
            $model->fileParticipants = UploadedFile::getInstance($model, 'fileParticipants');
            if ($model->photosFile !== null)
                $model->uploadPhotosFile();
            if ($model->presentDataFile !== null)
                $model->uploadPresentDataFile();
            if ($model->workDataFile !== null)
                $model->uploadWorkDataFile();
            if ($model->fileParticipants !== null)
                $model->uploadFileParticipants();
            $model->save(false);
            $model->GenerateNumber();
            $model->auto = null;
            $model->lessons = null;
            $model->teachers = null;
            $model->participants = null;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Добавлена группа '.$model->number);
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'modelTrainingGroupParticipant' => $modelTrainingGroupParticipant,
            'modelTrainingGroupLesson' => $modelTrainingGroupLesson,
            'modelTrainingGroupAuto' => $modelTrainingGroupAuto,
            'modelOrderGroup' => $modelOrderGroup,
            'modelTeachers' => $modelTeachers,
            'modelProjectThemes' => $modelProjectThemes,
            'modelExperts' => $modelExperts,
        ]);
    }

    /**
     * Updates an existing TrainingGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        if ($model->archive === 1)
        {
            Yii::$app->session->addFlash('danger', 'Невозможно редактировать архивную группу!');
            return $this->redirect(['/training-group/index']);
        }
        $model->delArr = [];
        $modelTrainingGroupParticipant = [new TrainingGroupParticipantWork];
        $modelTrainingGroupLesson = [new TrainingGroupLessonWork];
        $modelTrainingGroupAuto = [new TrainingGroupAuto];
        $modelOrderGroup = [new OrderGroupWork];
        $modelTeachers = [new TeacherGroupWork];
        $modelProjectThemes = [new GroupProjectThemesWork];
        $modelExperts = [new TrainingGroupExpertWork];

        $extEvents = TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['training_group_id' => $model->id])->orderBy(['participant.secondname' => SORT_ASC, 'participant.firstname' => SORT_ASC, 'participant.patronymic' => SORT_ASC])->all();
        if ($extEvents != null)
        {
            foreach ($extEvents  as $extEvent) {
                $model->certificatArr[] = $extEvent->certificat_number;
                $model->sendMethodArr[] = $extEvent->send_method_id;
                $model->idArr[] = $extEvent->id;
            }
        }
        $session = Yii::$app->session;
        if ($session->get("show") === null)
            $session->set("show", "common");

        if ($model->load(Yii::$app->request->post())) {
            $model->number = "";
            $model->photosFile = UploadedFile::getInstances($model, 'photosFile');
            $model->presentDataFile = UploadedFile::getInstances($model, 'presentDataFile');
            $model->workDataFile = UploadedFile::getInstances($model, 'workDataFile');
            $modelTrainingGroupParticipant = DynamicModel::createMultiple(TrainingGroupParticipantWork::classname());
            DynamicModel::loadMultiple($modelTrainingGroupParticipant, Yii::$app->request->post());
            $model->participants = $modelTrainingGroupParticipant;
            $modelTrainingGroupLesson = DynamicModel::createMultiple(TrainingGroupLessonWork::classname());
            DynamicModel::loadMultiple($modelTrainingGroupLesson, Yii::$app->request->post());
            $model->lessons = $modelTrainingGroupLesson;
            $modelTrainingGroupAuto = DynamicModel::createMultiple(TrainingGroupAuto::classname());
            DynamicModel::loadMultiple($modelTrainingGroupAuto, Yii::$app->request->post());
            $model->auto = $modelTrainingGroupAuto;
            $modelOrderGroup = DynamicModel::createMultiple(OrderGroupWork::classname());
            DynamicModel::loadMultiple($modelOrderGroup, Yii::$app->request->post());
            $model->orders = $modelOrderGroup;
            $modelTeachers = DynamicModel::createMultiple(TeacherGroupWork::classname());
            DynamicModel::loadMultiple($modelTeachers, Yii::$app->request->post());
            $model->teachers = $modelTeachers;
            $modelProjectThemes = DynamicModel::createMultiple(GroupProjectThemesWork::classname());
            DynamicModel::loadMultiple($modelProjectThemes, Yii::$app->request->post());
            $model->themes = $modelProjectThemes;
            $modelExperts = DynamicModel::createMultiple(TrainingGroupExpertWork::classname());
            DynamicModel::loadMultiple($modelExperts, Yii::$app->request->post());
            $model->experts = $modelExperts;

            $model->fileParticipants = UploadedFile::getInstance($model, 'fileParticipants');
            $model->certFile = UploadedFile::getInstance($model, 'certFile');
            //$model->save();

            //var_dump($model->workDataFile);
            if ($model->photosFile !== null)
                $model->uploadPhotosFile(10);
            if ($model->presentDataFile !== null)
                $model->uploadPresentDataFile(10);
            if ($model->workDataFile !== null)
                $model->uploadWorkDataFile(10);
            if ($model->fileParticipants !== null)
                $model->uploadFileParticipants();
            if ($model->certFile !== null)
                $model->uploadFileCert();
            if (count($model->getErrors()) == 0)
                $model->save(false);
            $model = $this->findModel($id);
            $model->auto = null;
            $model->lessons = null;
            $model->teachers = null;
            $model->participants = null;
            $model->GenerateNumber();
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменена группа '.$model->number);
            if (array_key_exists('deleteChoose', $_POST) && $_POST['deleteChoose'] !== "")
            {
                return $this->redirect('index?r=training-group/update&id=' . $model->id);
            }
            else
                return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'modelTrainingGroupParticipant' => $modelTrainingGroupParticipant,
            'modelTrainingGroupLesson' => $modelTrainingGroupLesson,
            'modelTrainingGroupAuto' => $modelTrainingGroupAuto,
            'modelOrderGroup' => $modelOrderGroup,
            'modelTeachers' => $modelTeachers,
            'modelProjectThemes' => $modelProjectThemes,
            'modelExperts' => $modelExperts,
        ]);
    }

    /**
     * Deletes an existing TrainingGroup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->delete();
        $number = $model->number;
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удалена группа '.$number);

        return $this->redirect(['index']);
    }



    /**
     * Finds the TrainingGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TrainingGroupWork the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TrainingGroupWork::findOne($id)) !== null) {
            return $model;
        }
        throw new NotFoundHttpException('The requested page does not exist.');
    }

    public function actionDeleteParticipant($id, $modelId)
    {
        $participant = TrainingGroupParticipantWork::find()->where(['id' => $id])->one();
        $name = $participant->participantWork->secondname . ' ' . $participant->participantWork->firstname . ' ' . $participant->participantWork->patronymic;
        $group = $participant->trainingGroupWork->number;
        $pasta = OrderGroupParticipantWork::find()->where(['group_participant_id' => $id])->all();
        if (count($pasta) == 0)
            $participant->delete();
        else
            Yii::$app->session->setFlash("danger", "Невозможно удалить ученика, фигурирующего в одном или нескольких приказах!");
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален обучающийся '.$name.' из группы '.$group);
        return $this->redirect('index?r=training-group/update&id='.$modelId);
    }

    public function actionDeleteTeacher($id, $modelId)
    {
        $teacher = TeacherGroupWork::find()->where(['id' => $id])->one();
        $name = $teacher->teacherWork->secondname . ' ' . $teacher->teacherWork->firstname . ' ' . $teacher->teacherWork->patronymic;
        $group = $teacher->trainingGroupWork->number;
        $teacher->delete();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален педагог '.$name.' из группы '.$group);
        return $this->redirect('index?r=training-group/update&id='.$modelId);
    }

    public function actionRemandParticipant($id, $modelId)
    {
        $participant = TrainingGroupParticipantWork::find()->where(['id' => $id])->one();
        $name = $participant->participantWork->secondname . ' ' . $participant->participantWork->firstname . ' ' . $participant->participantWork->patronymic;
        $group = $participant->trainingGroupWork->number;
        $participant->status = 1;
        $participant->save();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Отчислен обучающийся '.$name.' из группы '.$group);
        return $this->redirect('index?r=training-group/update&id='.$modelId);
    }

    public function actionUnremandParticipant($id, $modelId)
    {
        $participant = TrainingGroupParticipantWork::find()->where(['id' => $id])->one();
        $name = $participant->participantWork->secondname . ' ' . $participant->participantWork->firstname . ' ' . $participant->participantWork->patronymic;
        $group = $participant->trainingGroupWork->number;
        $participant->status = 0;
        $participant->save();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Восстановлен обучающийся '.$name.' группы '.$group);
        return $this->redirect('index?r=training-group/update&id='.$modelId);
    }

    public function actionUpdateParticipant($id)
    {
        $model = TrainingGroupParticipantWork::find()->where(['id' => $id])->one();

        if ($model->load(Yii::$app->request->post())) {
            $model->save(false);
            $name = $model->participantWork->secondname . ' ' . $model->participantWork->firstname . ' ' . $model->participantWork->patronymic;
            $group = TrainingGroupWork::find()->where(['id' => $model->training_group_id])->one();
            $modelTrainingGroupParticipant = [new TrainingGroupParticipantWork];
            $modelTrainingGroupLesson = [new TrainingGroupLessonWork];
            $modelTrainingGroupAuto = [new TrainingGroupAuto];
            $modelOrderGroup = [new OrderGroupWork];
            $modelTeachers = [new TeacherGroupWork];
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменен обучающийся '.$name.' группы '.$group->number);
            return $this->redirect('index?r=training-group/update&id='.$model->training_group_id);

        }
        return $this->render('update-participant', [
            'model' => $model,
        ]);
    }

    public function actionUpdateLesson($lessonId, $modelId)
    {
        $model = TrainingGroupLessonWork::find()->where(['id' => $lessonId])->one();
        if ($model->load(Yii::$app->request->post())) {
            $model->save(false);
            $group = TrainingGroupWork::find()->where(['id' => $modelId])->one();
            $modelTrainingGroupParticipant = [new TrainingGroupParticipantWork];
            $modelTrainingGroupLesson = [new TrainingGroupLessonWork];
            $modelTrainingGroupAuto = [new TrainingGroupAuto];
            $modelOrderGroup = [new OrderGroupWork];
            $modelProjectThemes = [new GroupProjectThemesWork];
            $modelExperts = [new TrainingGroupExpertWork];

            $modelTeachers = [new TeacherGroupWork];
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Изменено занятие '.$model->lesson_date.'('.$model->lesson_start_time.') группы '.$model->trainingGroup->number);
            return $this->render('update', [
                'model' => $group,
                'modelTrainingGroupParticipant' => $modelTrainingGroupParticipant,
                'modelTrainingGroupLesson' => $modelTrainingGroupLesson,
                'modelTrainingGroupAuto' => $modelTrainingGroupAuto,
                'modelOrderGroup' => $modelOrderGroup,
                'modelTeachers' => $modelTeachers,
                'modelProjectThemes' => $modelProjectThemes,
                'modelExperts' => $modelExperts,
            ]);
        }
        return $this->render('update-lesson', [
            'model' => $model,
        ]);
    }

    public function actionSendCertificats($group_id)
    {
        $certificats = CertificatWork::find()->joinWith(['trainingGroupParticipant tgp'])->where(['tgp.training_group_id' => $group_id])->all();

        $pIds = [];
        foreach ($certificats as $certificat) $pIds[] = $certificat->certificat_number;

        $model = new CertificatWork();
        $model->certificat_id = $pIds;
        $model->mass_send();

        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Отправлены сертификаты для группы ['.$group_id.']');


        return $this->render('view', [
            'model' => $this->findModel($group_id),
        ]);
    }

    public function actionDeleteLesson($id, $modelId)
    {
        $participant = TrainingGroupLessonWork::find()->where(['id' => $id])->one();
        $themes = LessonThemeWork::find()->where(['training_group_lesson_id' => $participant->id])->all();
        $visits = VisitWork::find()->where(['training_group_lesson_id' => $participant->id])->andWhere(['!=', 'status', 3])->all();
        if (count($themes) > 0 || count($visits) > 0)
        {
            Yii::$app->session->setFlash("danger", "Невозможно удалить занятие, т.к. присутствуют связанные с ним сведения о явке/неявке обучающихся и/или сведения о теме занятия в учебно-тематическом плане");
            return $this->redirect('index?r=training-group/update&id='.$modelId);
        }
        $visits = VisitWork::find()->where(['training_group_lesson_id' => $participant->id])->all();
        foreach ($visits as $visit)
            $visit->delete();
        $participant->delete();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удалено занятие '.$participant->lesson_date.'('.$participant->lesson_start_time.') группы '.$participant->trainingGroup->number);
        return $this->redirect('index?r=training-group/update&id='.$modelId);
    }

    public function actionDeleteOrder($id, $modelId)
    {
        $order = OrderGroupWork::find()->where(['id' => $id])->one();
        $number = $order->documentOrderWork->documentNumberString;
        $group = $order->trainingGroupWork->number;
        $order->delete();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален приказ '.$number.' группы '.$group);
        return $this->redirect('index?r=training-group/update&id='.$modelId);
    }

    public function actionGetFile($fileName = null, $modelId = null, $type = null)
    {
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Загружен файл '.$fileName);


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

            //header('Content-Description: File Transfer');
            //header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=' . $downloadYadi->filename);
            //header('Content-Transfer-Encoding: binary');
            header('Content-Length: ' . $downloadYadi->file->size);

            $downloadYadi->file->download($fp);

            fseek($fp, 0);
        }
    }

    public function actionDeleteFile($fileName = null, $modelId = null, $type = null)
    {
        $model = TrainingGroupWork::find()->where(['id' => $modelId])->one();

        if ($fileName !== null && !Yii::$app->user->isGuest && $modelId !== null) {

            $result = '';
            $split = '';
            if ($type == 'photos') $split = explode(" ", $model->photos);
            if ($type == 'present_data') $split = explode(" ", $model->present_data);
            if ($type == 'work_data') $split = explode(" ", $model->work_data);
            $deleteFile = '';
            for ($i = 0; $i < count($split) - 1; $i++) {
                if ($split[$i] !== $fileName) {
                    $result = $result . $split[$i] . ' ';
                } else
                    $deleteFile = $split[$i];
            }
            if ($type == 'photos') $model->photos = $result;
            if ($type == 'present_data') $model->present_data = $result;
            if ($type == 'work_data') $model->work_data = $result;
            $model->save(false);
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Удален файл ' . $deleteFile);
        }
        return $this->redirect('index?r=training-group/update&id='.$model->id);
    }

    public function actionGetKug($training_group_id)
    {
        $group = TrainingGroupWork::find()->where(['id' => $training_group_id])->one();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Выгружен КУГ группы ' . $group->number);
        ExcelWizard::DownloadKUG($training_group_id);
    }

    public function actionGetArchive($group_id)
    {
        $parts = TrainingGroupParticipantWork::find()->where(['training_group_id' => $group_id])->all();
        $partsID = [];
        foreach ($parts as $part)
            $partsID[] = $part->id;
        $certificats = CertificatWork::find()->where(['IN', 'training_group_participant_id', $partsID])->all();

        if (empty($certificats))
        {
            Yii::$app->session->setFlash('danger', 'Невозможно скачать архив сертификатов, если сертификаты не выданы!');
            return $this->redirect('index?r=training-group/view&id='.$group_id);
        }
        else
        {
            FileHelper::createDirectory(Yii::$app->basePath.'/download/'.Yii::$app->user->identity->getId().'/');
            foreach ($certificats as $certificat)
                PdfWizard::DownloadCertificat($certificat->id, 'server');
            $archive = new CertificatWork();
            $archive->archiveDownload();
        }
    }

    public function actionDownloadExcel($group_id)
    {
        $group = TrainingGroupWork::find()->where(['id' => $group_id])->one();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Выгружен журнал группы ' . $group->number);
        ExcelWizard::DownloadJournal($group_id);
    }

    public function actionDownloadJournal($group_id)
    {
        $group = TrainingGroupWork::find()->where(['id' => $group_id])->one();
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Выгружен журнал и КУГ группы ' . $group->number);
        ExcelWizard::DownloadJournalAndKUG($group_id);
    }

    public function actionSubcat()
    {
        if ($id = Yii::$app->request->post('id')) {
            $operationPosts = BranchWork::find()
                ->where(['id' => $id])
                ->count();

            if ($operationPosts > 0) {
                $operations = AuditoriumWork::find()
                    ->where(['branch_id' => $id])
                    ->all();
                echo "<option value=null>" . "Вне отдела" . "</option>";
                foreach ($operations as $operation)
                    echo "<option value='" . $operation->id . "'>" . $operation->name . ' (' . $operation->text . ')' . "</option>";
            } else
                echo "<option>-</option>";

        }
    }

    public function actionParse()
    {
        //var_dump(Yii::$app->basePath.'/upload/files/bitrix/groups/group1.xls');
        /*$inputType = \PHPExcel_IOFactory::identify(Yii::$app->basePath.'/upload/files/bitrix/groups/group2.xls');
        $reader = \PHPExcel_IOFactory::createReader($inputType);
        $inputData = $reader->load(Yii::$app->basePath.'/upload/files/bitrix/groups/group2.xls');
        $writer = \PHPExcel_IOFactory::createWriter($inputData, 'Excel2007');
        $inputData = $writer->save(Yii::$app->basePath.'/upload/files/bitrix/groups/group2new.xls');

        $newReader = \PHPExcel_IOFactory::createReader('Excel2007');
        $inputData = $newReader->load(Yii::$app->basePath.'/upload/files/bitrix/groups/group2new.xls');
        var_dump($inputData->getActiveSheet()->getCellByColumnAndRow(3, 3)->getValue());*/

        ExcelWizard::GetAllParticipants("group2.xls");
    }

    public function actionShowCommon($modelId = null)
    {
        $session = Yii::$app->session;
        $session->set("show", "common");
        if ($modelId == null)
            return $this->redirect('index?r=training-group/create');
        return $this->redirect('index?r=training-group/update&id='.$modelId);
    }

    public function actionShowParts($modelId = null)
    {
        $session = Yii::$app->session;
        $session->set("show", "parts");
        if ($modelId == null)
            return $this->redirect('index?r=training-group/create');
        return $this->redirect('index?r=training-group/update&id='.$modelId);
    }

    public function actionShowSchedule($modelId = null)
    {
        $session = Yii::$app->session;
        $session->set("show", "schedule");
        if ($modelId == null)
            return $this->redirect('index?r=training-group/create');
        return $this->redirect('index?r=training-group/update&id='.$modelId);
    }

    public function actionAmnesty ($id)
    {
        $errorsAmnesty = new GroupErrorsWork();
        $errorsAmnesty->GroupAmnesty($id);
        return $this->redirect('index?r=training-group/view&id='.$id);
    }

    public function actionDeleteTheme($id, $modelId)
    {
        $gpt = GroupProjectThemesWork::find()->where(['id' => $id])->one();

        $tgps = TrainingGroupParticipantWork::find()->where(['group_project_themes_id' => $id])->all();

        foreach ($tgps as $tgp)
        {
            $tgp->group_project_themes_id = null;
            $tgp->save();
        }

        $gpt->delete();
        
        return $this->redirect('index?r=training-group/update&id=' . $modelId);
    }

    public function actionDeleteExpert($id, $modelId)
    {
        $gpt = TrainingGroupExpertWork::find()->where(['id' => $id])->one();

        $gpt->delete();
        
        return $this->redirect('index?r=training-group/update&id=' . $modelId);
    }

    public function actionConfirmTheme($id, $modelId)
    {
        $gpt = GroupProjectThemesWork::find()->where(['id' => $id])->one();
        $gpt->confirm = 1;
        $gpt->save();
        
        return $this->redirect('index?r=training-group/update&id=' . $modelId);
    }

    public function actionDeclineTheme($id, $modelId)
    {
        $gpt = GroupProjectThemesWork::find()->where(['id' => $id])->one();

        $tgp = TrainingGroupParticipantWork::find()->where(['group_project_themes_id' => $gpt->project_theme_id])->all();

        if (count($tgp) > 0)
            Yii::$app->session->setFlash('danger', 'Невозможно отклонить тему, прикрепленную к одному или нескольким ученикам группы!');
        else
        {
            $gpt->confirm = 0;
            $gpt->save();
        }
        
        return $this->redirect('index?r=training-group/update&id=' . $modelId);
    }

    //Проверка на права доступа к CRUD-операциям
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest || Yii::$app->user->identity == null)
        {
            $this->redirect(['/site/login']);
            return 0;
        }
            
        if (!RoleBaseAccess::CheckAccess($action->controller->id, $action->id, Yii::$app->user->identity->getId(), "group",
            $action->id == 'view' || $action->id == 'update' ? $_GET['id'] : 0)) {
            return $this->redirect(['/site/error-access']);
        }
        return parent::beforeAction($action); // TODO: Change the autogenerated stub
    }
}
