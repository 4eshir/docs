<?php

namespace app\controllers;

use app\models\common\TrainingGroup;
use app\models\components\Logger;
use app\models\work\ErrorsWork;
use app\models\work\GroupErrorsWork;
use app\models\work\ProgramErrorsWork;
use app\models\work\TrainingProgramWork;
use app\models\work\UserRoleWork;
use app\models\work\UserWork;
use Yii;
use yii\web\Controller;

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
        //$users = UserWork::find()->joinWith(['userRoles userRoles'])->all();

        $messages = [];
        foreach ($users as $user)
        {
            $errors = new ErrorsWork();
            //$errorsTraining = $errors->ErrorsElectronicJournalSubsystem($user, 1);
            $role = $user->userRoles[0]->role_id;
            $errorsTraining = $errors->test($role, $user);
            if ($errorsTraining !== '')
            {
                $string = 'Еженедельная сводка об ошибках в ЦСХД. Внимание, в данной сводке выводятся только критические ошибки!' . '<br><br><div style="max-width: 800px;">';
                $string .= $errorsTraining . '</div>';   // тут будет лежать всё то, что отправится пользователю
                //$string .= $errors->ForAdmin() . '</div>';
                $string .= '<br><br> Чтобы узнать больше перейдите на сайт ЦСХД: https://index.schooltech.ru/';
                $string .= '<br>---------------------------------------------------------------------------';
                $messages[] = Yii::$app->mailer->compose()
                    ->setFrom('noreply@schooltech.ru')
                    ->setTo($user->username)
                    ->setSubject('Краткая сводка по ЦСХД')
                    ->setHtmlBody( $string . '<br><br>Пожалуйста, обратите внимание, что это сообщение было сгенерировано и отправлено в автоматическом режиме. Не отвечайте на него.');
                Logger::WriteLog(1, 'Пользователю ' . $user->username . ' отправлено сообщение об ошибках в системе');
            }
        }
        Yii::$app->mailer->sendMultiple($messages);
    }

}
