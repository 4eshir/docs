<?php

namespace app\commands\participants;

use app\helpers\participants\ForeignEventParticipantsHelper;
use yii\console\Controller;
use yii\helpers\Console;

class ParticipantsController extends Controller
{
    // удаление учеников, не прикрепленных к группам или мероприятиям
    public function actionDeleteUnnecessaryParticipants()
    {
        $participants = ForeignEventParticipantsHelper::getUnlinkedParticipants();

        foreach ($participants as $participant) $participant->delete();
        Console::stdout('Удалено обучающихся: '.count($participants));

    }
}