<?php

namespace app\controllers;

use app\models\common\TrainingGroup;
use app\models\components\Logger;
use app\models\work\DocumentOrderWork;
use app\models\work\ErrorsWork;
use app\models\work\EventErrorsWork;
use app\models\work\EventWork;
use app\models\work\ForeignEventErrorsWork;
use app\models\work\ForeignEventWork;
use app\models\work\GroupErrorsWork;
use app\models\work\OrderErrorsWork;
use app\models\work\OrderGroupWork;
use app\models\work\ProgramErrorsWork;
use app\models\work\RoleFunctionRoleWork;
use app\models\work\TrainingGroupWork;
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
        $groups = TrainingGroupWork::find()->where(['archive' => 0])->all();
        foreach ($groups as $group)
        {
            $errorsGroupCheck = new GroupErrorsWork();
            $errorsGroupCheck->CheckErrorsJournal($group->id);
        }
    }

    public function actionTrainingGroupErrors()
    {
        $groups = TrainingGroupWork::find()->where(['archive' => 0])->all();
        foreach ($groups as $group)
        {
            $errorsGroupCheck = new GroupErrorsWork();
            $errorsGroupCheck->CheckErrorsTrainingGroup($group->id);
        }
    }

    public function actionDocumentOrderErrors()
    {
        $orders = DocumentOrderWork::find()->where(['not like', 'order_name', 'резерв'])->all();
        foreach ($orders as $order)
        {
            $errorsOrderCheck = new OrderErrorsWork();
            $errorsOrderCheck->CheckDocumentOrder($order->id);
        }
    }

    public function actionEventAndForeignEventErrors()
    {
        $events = EventWork::find()->all();
        foreach ($events as $event)
        {
            $errorsEventCheck = new EventErrorsWork();
            $errorsEventCheck->CheckErrorsEvent($event->id);
        }

        $foreignEvents = ForeignEventWork::find()->all();
        foreach ($foreignEvents as $foreignEvent)
        {
            $errorsForeignEventCheck = new ForeignEventErrorsWork();
            $errorsForeignEventCheck->CheckErrorsForeignEvent($foreignEvent->id);
        }
    }

    public function actionMessageErrors()
    {
        $users = UserWork::find()->all();
        $functionsSet = RoleFunctionRoleWork::find();
        //$users = UserWork::find()->joinWith(['userRoles userRoles'])->all();

        $messages = [];
        foreach ($users as $user)
        {
            $functions = [];
            foreach ($user->userRoles as $role)
            {
                $function = $functionsSet->where(['role_id' => $role->role_id])->all();
                foreach ($function as $oneFunction)
                    $functions[] = $oneFunction->role_function_id;
            }
            $functions = array_unique(array_intersect($functions, [12, 13, 14, 15, 16, 24, 32]), SORT_NUMERIC);

            if (count($functions) !== 0)
            {
                asort($functions);

                $errors = new ErrorsWork();
                $errorsSystem = $errors->SystemCriticalMessage($user, $functions);
                if ($errorsSystem !== '')
                {
                    $string = 'Еженедельная сводка об ошибках в ЦСХД. Внимание, в данной сводке выводятся только критические ошибки!' . '<br><br><div style="max-width: 800px;">';
                    $string .= $errorsSystem . '</div>';   // тут будет лежать всё то, что отправится пользователю
                    $string .= '<br><br> Чтобы узнать больше перейдите на сайт ЦСХД: https://index.schooltech.ru/';
                    $string .= '<br>---------------------------------------------------------------------------';
                    $messages[] = Yii::$app->mailer->compose()
                        ->setFrom('noreply@schooltech.ru')
                        ->setTo($user->username)
                        ->setSubject('Cводка критических ошибок по ЦСХД')
                        ->setHtmlBody( $string . '<br><br>Пожалуйста, обратите внимание, что это сообщение было сгенерировано и отправлено в автоматическом режиме. Не отвечайте на него.');
                    Logger::WriteLog(1, 'Пользователю ' . $user->username . ' отправлено сообщение об ошибках в системе');
                }
            }
        }
        Yii::$app->mailer->sendMultiple($messages);
    }

}