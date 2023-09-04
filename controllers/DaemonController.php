<?php

namespace app\controllers;

use app\models\common\Container;
use app\models\common\ContractErrors;
use app\models\common\MaterialObjectErrors;
use app\models\common\TrainingGroup;
use app\models\components\Logger;
use app\models\work\BackupDifferenceWork;
use app\models\work\BackupVisitWork;
use app\models\work\ContainerErrorsWork;
use app\models\work\ContainerWork;
use app\models\work\ContractErrorsWork;
use app\models\work\ContractWork;
use app\models\work\DocumentOrderWork;
use app\models\work\ErrorsWork;
use app\models\work\EventErrorsWork;
use app\models\work\EventWork;
use app\models\work\ForeignEventErrorsWork;
use app\models\work\ForeignEventWork;
use app\models\work\GroupErrorsWork;
use app\models\work\InvoiceErrorsWork;
use app\models\work\InvoiceWork;
use app\models\work\MaterialObjectErrorsWork;
use app\models\work\MaterialObjectWork;
use app\models\work\OrderErrorsWork;
use app\models\work\OrderGroupWork;
use app\models\work\ProgramErrorsWork;
use app\models\work\RoleFunctionRoleWork;
use app\models\work\TrainingGroupWork;
use app\models\work\TrainingProgramWork;
use app\models\work\UserRoleWork;
use app\models\work\UserWork;
use app\models\work\VisitWork;
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

    public function actionContractAndInvoiceAndContainerErrors()
    {
        $contract = ContractWork::find()->all();
        foreach ($contract as $one)
        {
            $errorsCheck = new ContractErrorsWork();
            $errorsCheck->CheckErrorsContract($one->id);
        }

        $invoice = InvoiceWork::find()->all();
        foreach ($invoice as $one)
        {
            $errorsCheck = new InvoiceErrorsWork();
            $errorsCheck->CheckErrorsInvoice($one->id);
        }

        $container = ContainerWork::find()->all();
        foreach ($container as $one)
        {
            $errorsCheck = new ContainerErrorsWork();
            $errorsCheck->CheckErrorsContainer($one->id);
        }
    }

    public function actionMaterialObjectErrors()
    {
        $matObj = MaterialObjectWork::find()->all();
        foreach ($matObj as $one)
        {
            $errorsCheck = new MaterialObjectErrorsWork();
            $errorsCheck->CheckErrorsMaterialObject($one->id);
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

    /*--Задолбал этот ЦДНТТ--
     * | Каждое утро сверяем вчерашний бэкап с текущим                                    |
     * | При обнаружении расхождений - сигнализируем об этом в таблицу backup_difference  |
     * | Создаем новый бэкап групп и явок для ЦДНТТ на основе текущих данных              |
     */

    public function actionBackupVisits()
    {
        ini_set('memory_limit', '2048MB');
        set_time_limit(10000);

        $bVisits = BackupVisitWork::find()->orderBy(['id' => SORT_ASC])->all();
        $cVisits = VisitWork::find()->orderBy(['id' => SORT_ASC])->all();

        for ($i = 0; $i < count($bVisits); $i++)
        {
            if ($bVisits[$i]->id == $cVisits[$i]->id && $bVisits[$i]->status !== 0 && $cVisits == 0)
            {
                $diff = new BackupDifferenceWork();
                $diff->visit_id = $bVisits[$i]->id;
                $diff->old_status = $bVisits[$i]->status;
                $diff->new_status = $cVisits[$i]->status;
                $diff->date = date('Y-m-d');
                $diff->save();
            }
        }

        foreach ($bVisits as $bVisit) $bVisit->delete();

        foreach ($cVisits as $cVisit)
        {
            $backup = new BackupVisitWork();
            $backup->id = $cVisit->id;
            $backup->foreign_event_participant_id = $cVisit->foreign_event_participant_id;
            $backup->training_group_lesson_id = $cVisit->training_group_lesson_id;
            $backup->status = $cVisit->status;
            $backup->save();
        }
    }

}
