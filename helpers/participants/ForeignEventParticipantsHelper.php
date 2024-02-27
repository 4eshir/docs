<?php

namespace app\helpers\participants;

use app\models\work\EventWork;
use app\models\work\ForeignEventParticipantsWork;
use app\models\work\ForeignEventWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;

class ForeignEventParticipantsHelper
{
    public static function getUnlinkedParticipants()
    {
        $foreignEvents = TeacherParticipantWork::find()->select('participant_id')->distinct()->all();
        $groupsParticipants = TrainingGroupParticipantWork::find()->select('participant_id')->distinct()->all();

        $unnecessaryIds = [];
        foreach ($foreignEvents as $event) $unnecessaryIds[] = $event->participant_id;
        foreach ($groupsParticipants as $group) $unnecessaryIds[] = $group->participant_id;

        return ForeignEventParticipantsWork::find()->where(['NOT IN', 'id', $unnecessaryIds])->all();
    }
}