<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use app\models\common\ForeignEventParticipants;
use app\models\LoginForm;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\ForeignEventWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\VisitWork;
use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Console;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SupCommandsController extends Controller
{

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

        $this->stdout($message."\n", Console::FG_RED);
        //echo '<color="green">'.$message.'</color>' . "\n";

        return ExitCode::OK;
    }


    // --Поиск расхождений в количестве участников и победителей мероприятий--
    // -- Формат: Мероприятие | ФИО | Кол-во фактов участия | Кол-во фактов побед/приз. --
    // -- Исходная таблица: foreign_event
    public function actionCheckEventDifference()
    {
        $events = ForeignEventWork::find()->all();

        foreach ($events as $event)
        {
            $allDistinctParticipants = TeacherParticipantWork::find()->select('participant_id')->distinct()->where(['foreign_event_id' => $event->id])->all();
            $pIds = [];
            foreach ($allDistinctParticipants as $one) $pIds[] = $one->participant_id;

            foreach ($pIds as $id)
            {
                $participant = ForeignEventParticipantsWork::find()->where(['id' => $id])->one();
                $partFacts = count(TeacherParticipantWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['participant_id' => $id])->all());
                $prizeFacts = count(ParticipantAchievementWork::find()->where(['foreign_event_id' => $event->id])->andWhere(['participant_id' => $id])->all());

                if ($partFacts < $prizeFacts)
                    $this->stdout($event->name.' | '.$participant->fullName.' | '.$partFacts. ' | '.$prizeFacts ."\n", Console::FG_RED);
            }
        }

        return ExitCode::OK;
    }

    public function actionCheckTime()
    {
        $count = 1000000;

        $start1 = microtime(true);
        for ($i = 0; $i < $count; $i++)
            $res = TrainingGroupParticipantWork::find()->where(['training_group_id' => $i])->orWhere(['participant_id' => $i])->all();

        $this->stdout('Time 1: '.round(microtime(true) - $start1, 2)."\n", Console::FG_PURPLE);

        $start2 = microtime(true);
        $res = TrainingGroupParticipantWork::find()->all();
        $tr = [];
        for ($i = 0; $i < $count; $i++)
            for ($j = 0; $j < count($res); $j++)
                if ($res[$j]->participant_id == $i && $res[$j]->training_group_id == $i)
                    $tr[] = $res[$j];

        $this->stdout('Time 2: '.round(microtime(true) - $start2, 2), Console::FG_GREEN);
    }

    public function actionCheckMemory($type)
    {
        $count = 100000;

        if ($type == 1)
        {
            $start1 = memory_get_usage();
            for ($i = 0; $i < $count; $i++)
                $res = TrainingGroupParticipantWork::find()->where(['training_group_id' => $i])->orWhere(['participant_id' => $i])->all();

            $this->stdout('Time 1: '.round(memory_get_usage() - $start1, 2)."\n", Console::FG_PURPLE);
        }
        else
        {
            $start2 = memory_get_usage();
            $res = TrainingGroupParticipantWork::find()->all();
            $tr = [];
            for ($i = 0; $i < $count; $i++)
                for ($j = 0; $j < count($res); $j++)
                    if ($res[$j]->participant_id == $i || $res[$j]->training_group_id == $i)
                        $tr[] = $res[$j];

            $this->stdout('Time 2: '.round(memory_get_usage() - $start2, 2), Console::FG_PURPLE);
        }



    }
}
