<?php

namespace app\controllers;

use app\models\common\TrainingGroup;
use app\models\common\User;
use app\models\components\Logger;
use app\models\components\UserRBAC;
use app\models\work\AuditoriumWork;
use app\models\DynamicModel;
use app\models\work\GroupErrorsWork;
use app\models\work\ProgramErrorsWork;
use app\models\work\TrainingProgramWork;
use app\models\work\UserWork;
use Yii;
use app\models\work\BranchWork;
use app\models\SearchBranch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

class DaemonController extends Controller
{

    public function actionProgramErrors()
    {
        $programs = TrainingProgramWork::find()->all();
        foreach ($programs as $program)
        {
            $errorsProgramCheck = new ProgramErrorsWork();
            $errorsProgramCheck->CheckErrorsTrainingProgram($program->id);
        }
    }

    public function actionJournalErrors()
    {
        $groups = TrainingGroup::find()->where(['archive' => 0])->all();
        foreach ($groups as $group)
        {
            $errorsGroupCheck = new GroupErrorsWork();
            $errorsGroupCheck->CheckErrorsJournal($group->id);
        }
    }

    public function actionTrainingGroupErrors()
    {
        $groups = TrainingGroup::find()->where(['archive' => 0])->all();
        foreach ($groups as $group)
        {
            $errorsGroupCheck = new GroupErrorsWork();
            $errorsGroupCheck->CheckErrorsTrainingGroup($group->id);
        }
    }

    public function actionMessageErrors()
    {
        $users = UserWork::find()->all();
        foreach ($users as $user)
        {
            /*вот тут функция сбора всех ошибок по таблицам связкам*/
            $string = '';   // тут будет лежать всё то, что отправится пользоватею
            Yii::$app->mailer->compose()
                ->setFrom('noreply@schooltech.ru')
                ->setTo($user->username)
                ->setSubject('Краткая сводка по ЦСХД')
                ->setHtmlBody( $string . '<br><br>Пожалуйста, обратите внимание, что это сообщение было сгенерировано и отправлено в автоматическом режиме. Не отвечайте на него.')
                ->send();
            Logger::WriteLog(1, 'Пользователю ' . $user->username . ' отправлено сообщение об ошибках в системе');
        }
    }

}
