<?php

namespace app\controllers;

use app\models\common\DocumentOrder;
use app\models\common\DocumentOut;
use app\models\common\Feedback;
use app\models\common\People;
use app\models\common\PeoplePositionBranch;
use app\models\common\User;
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
use app\models\extended\UserExtended;
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
                        'actions' => ['login', 'error', 'forgot-password'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'index', 'index-docs-out', 'create-docs-out', 'add-admin', 'feedback', 'feedback-answer', 'temp'],
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

        $model = new Feedback();
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
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

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

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Logger::WriteLog(Yii::$app->user->identity->getId(), 'Выполнен выход из системы');
        Yii::$app->user->logout();

        return $this->goHome();
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
                    ->setFrom('no-reply-schooltech@mail.ru')
                    ->setTo($model->email)
                    ->setSubject('Восстановление пароля')
                    ->setTextBody($string)
                    ->setHtmlBody('Ваш новый пароль: '.$string)
                    ->send();
                $user = User::find()->where(['username' => $model->email])->one();
                $user->password_hash = Yii::$app->security->generatePasswordHash($string);
                $user->save();
                Logger::WriteLog(Yii::$app->user->identity->getId(), 'Сброшен пароль для пользователя '.$model->email);
                Yii::$app->session->addFlash('success', 'Вам на почту было отправлено письмо с новым паролем (проверьте папку "Спам"!).');
                return $this->redirect(['/site/login']);
            }
            else
                Yii::$app->session->addFlash('danger', 'Не найден пользователь с таким e-mail.');

        }
        return $this->render('forgot-password', ['model' => $model]);
    }

    public function actionCreateOutdocs()
    {

    }

    public function actionTemp()
    {
        $orders = DocumentOrder::find()->all();
        foreach ($orders as $order)
        {
            $order->type = 1;
            $order->save(false);
        }
    }

}

