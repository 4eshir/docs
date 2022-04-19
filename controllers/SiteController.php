<?php

namespace app\controllers;

use app\models\components\ArraySqlConstructor;
use app\models\components\ExcelWizard;
use app\models\work\DocumentOrderWork;
use app\models\work\DocumentOutWork;
use app\models\work\FeedbackWork;
use app\models\work\PeopleWork;
use app\models\work\TrainingGroupWork;
use app\models\common\Log;
use app\models\work\PeoplePositionBranchWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TeacherParticipantBranchWork;
use app\models\work\UserWork;
use app\models\components\Logger;
use app\models\extended\FeedbackAnswer;
use app\models\ForgotPassword;
use app\models\SearchDocumentOut;
use app\models\SearchOutDocsModel;
use Yii;
use yii\console\ExitCode;
use yii\filters\AccessControl;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\extended\DocumentOutExtended;


class SiteController extends Controller
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
                        'actions' => ['login', 'error', 'forgot-password', 'error-access', 'temp'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'index-docs-out', 'create-docs-out', 'add-admin', 'feedback', 'feedback-answer', 'temp', 'error-access'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }
    /**
     * This command echoes what you have entered as the message.
     * @param string $message the message to be echoed.
     * @return int Exit code
     */
    public function actionIndex($message = 'hello world')
    {
        return $this->render('index');
    }

    public function actionFeedback()
    {
        if (Yii::$app->user->isGuest)
            return $this->redirect(['/site/login']);

        $model = new FeedbackWork();
        if ($model->load(Yii::$app->request->post()))
        {
            $model->user_id = Yii::$app->user->identity->getId();
            $model->save();
            Yii::$app->session->addFlash('success', 'Спасибо за Ваше обращение!');

            return $this->redirect(['site/feedback']);
        }
        return $this->render('feedback', ['model' => $model]);
    }

    public function actionFeedbackAnswer($type = null)
    {
        $model = new FeedbackAnswer();
        $model->type = $type;

        if ($model->load(Yii::$app->request->post()) && $model->validate())
        {
            $model->loadFeedback();
        }

        return $this->render('feedback-answer', ['model' => $model]);
    }

    public function actionLogin()
    {
        if (Yii::$app->session->get('userSessionTimeout') !== 60 * 60 * 24 * 100)
            Yii::$app->session->set('userSessionTimeout', 60 * 60 * 24 * 100);

        //if (!Yii::$app->user->isGuest) {
        //    return $this->goHome();
        //}

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Выполнен вход в систему');
            return $this->render('index');
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    public function actionErrorAccess()
    {
        return $this->render('error-access');
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout($from = null)
    {
        if ($from == '1')
        {
            Logger::WriteLog(Yii::$app->user->identity->getId(), 'Выполнен выход из системы');
            Yii::$app->user->logout();

            return $this->goHome();
        }
        return "";
    }

    public function actionIndexDocsOut()
    {
        $searchModel = new SearchDocumentOut();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('/docs-out/index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionForgotPassword()
    {
        $model = new ForgotPassword();
        if ($model->load(Yii::$app->request->post()))
        {
            if ($model->validateEmail())
            {
                $string = Yii::$app->security->generateRandomString(8);
                Yii::$app->mailer->compose()
                    ->setFrom('noreply@schooltech.ru')
                    ->setTo($model->email)
                    ->setSubject('Восстановление пароля')
                    ->setTextBody($string)
                    ->setHtmlBody('Вы запросили восстановление пароля в системе электронного документооборота ЦСХД (https://index.schooltech.ru/)<br>Ваш новый пароль: '.$string.'<br><br>Пожалуйста, обратите внимание, что это сообщение было сгенерировано и отправлено в автоматическом режиме. Не отвечайте на него.')
                    ->send();
                $user = UserWork::find()->where(['username' => $model->email])->one();
                $user->password_hash = Yii::$app->security->generatePasswordHash($string);
                $user->save();
                Logger::WriteLog(1, 'Сброшен пароль для пользователя '.$model->email);
                Yii::$app->session->addFlash('success', 'Вам на почту было отправлено письмо с новым паролем (проверьте папку "Спам"!).');
                return $this->redirect(['/site/login']);
            }
            else
                Yii::$app->session->addFlash('danger', 'Не найден пользователь с таким e-mail.');

        }
        return $this->render('forgot-password', ['model' => $model]);
    }

    public function actionTemp()
    {
        $start_date = '2021-01-01';
        $end_date = '2021-12-31';
        $groups = TrainingGroupWork::find()->where(['IN', 'id', (new Query())->select('id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])])
            ->orWhere(['IN', 'id', (new Query())->select('id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])])
            ->orWhere(['IN', 'id', (new Query())->select('id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])])
            ->orWhere(['IN', 'id', (new Query())->select('id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])])
            ->all();
        
        $gIds = [];
        
        foreach ($groups as $group) $gIds[] = $group->id;
        return $gIds;

        $fps = ForeignEventParticipants::find()->all();
        $ids = [];
        foreach ($fps as $fp)
        {
            $parts = TrainingGroupParticipantWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['participant_id' => $fp->id])->andWhere(['IN', 'trainingGroup.id', $gIds])->all();
            if (count($parts) > 0)
            {
                $f1 = 0;
                $f2 = 0;
                foreach ($parts as $part)
                {
                    if ($part->trainingGroup->budget == 0)
                        $f1 = 1;
                    else
                        $f2 = 1;
                }
                if ($f1 == 1 && f2 == 1)
                    $ids[] = $part->participant_id;
            }
        }

        $fps = ForeignEventParticipants::find()->where(['IN', 'id', $ids])->all();

        foreach ($fps as $fp)
            echo $fp->fullName.' '.$fp->birthdate;
        
        /*
        $tp = TeacherParticipantWork::find()->all();
        foreach ($tp as $one) {
            $newBranch = TeacherParticipantBranchWork::find()->where(['teacher_participant_id' => $one->id])->one();
            if ($newBranch == null)
            {
                $newBranch = new TeacherParticipantBranchWork();
                $newBranch->teacher_participant_id = $one->id;
                $newBranch->branch_id = $one->branch_id;
                $newBranch->save();
            }
        }
        */
    }



    /*public function beforeAction($action)
    {
        if (!parent::beforeAction($action))
        {
            return false;
        } // Check only when the user is logged in
        if ( !Yii::$app->user->isGuest)
        {
            if (Yii::$app->session['userSessionTimeout'] < 60 * 60 * 24 * 100)
            {
                //Logger::WriteLog(Yii::$app->user->identity->getId(), 'Выполнен выход из системы');
                //Yii::$app->user->logout();

                //return $this->goHome();
            }
            else {
                Yii::$app->session->set('userSessionTimeout', 60 * 60 * 24 * 100);
                return true;
            }
        }
        else {
            return true;
        }
    }*/
}

