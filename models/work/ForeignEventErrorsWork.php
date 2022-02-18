<?php

namespace app\models\work;

use app\models\common\ForeignEventErrors;
use Yii;


class ForeignEventErrorsWork extends ForeignEventErrors
{
    public function ForeignEventAmnesty ($modelForeignEventID)
    {
        $errors = ForeignEventErrorsWork::find()->where(['foreign_event_id' => $modelForeignEventID, 'time_the_end' => null, 'amnesty' => null])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = 1;
            $err->save();
        }
    }

    private function NoAmnesty ($modelForeignEventID)
    {
        $errors = ForeignEventErrorsWork::find()->where(['foreign_event_id' => $modelForeignEventID, 'time_the_end' => null, 'amnesty' => 1])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = null;
            $err->save();
        }
    }

    private function CheckDate ($modelForeignEventID, $foreignEvent)
    {
        $err = ForeignEventErrorsWork::find()->where(['foreign_event_id' => $modelForeignEventID, 'time_the_end' => null, 'errors_id' => 22])->all();

        foreach ($err as $oneErr)
        {
            if ($foreignEvent->start_date < $foreignEvent->finish_date)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $foreignEvent->start_date > $foreignEvent->finish_date)
        {
            $this->foreign_event_id = $modelForeignEventID;
            $this->errors_id = 22;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckCity ($modelForeignEventID, $foreignEvent)
    {
        $err = ForeignEventErrorsWork::find()->where(['foreign_event_id' => $modelForeignEventID, 'time_the_end' => null, 'errors_id' => 23])->all();

        foreach ($err as $oneErr)
        {
            if ($foreignEvent->city !== NULL && $foreignEvent->city !== '')     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if ((count($err) === 0 && $foreignEvent->city === NULL) || (count($err) === 0 && $foreignEvent->city === ''))
        {
            $this->foreign_event_id = $modelForeignEventID;
            $this->errors_id = 23;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckParticipant ($modelForeignEventID, $participants)
    {
        $err = ForeignEventErrorsWork::find()->where(['foreign_event_id' => $modelForeignEventID, 'time_the_end' => null, 'errors_id' => 24])->all();
        $participantCount = count($participants);

        foreach ($err as $oneErr)
        {
            if ($participantCount !== 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $participantCount === 0)
        {
            $this->foreign_event_id = $modelForeignEventID;
            $this->errors_id = 24;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckParticipantGroup ($modelForeignEventID, $foreignEvent, $participants)
    {
        $err = ForeignEventErrorsWork::find()->where(['foreign_event_id' => $modelForeignEventID, 'time_the_end' => null, 'errors_id' => 25])->all();
        $flag = false;

        $groupsParticipantSet = TrainingGroupParticipantWork::find();
        $groupSet = TrainingGroupWork::find();
        $now = $foreignEvent->finish_date;

        foreach ($participants as $participant)
        {
            $groupsParticipant = $groupsParticipantSet->where(['participant_id' => $participant->id])->all();
            foreach ($groupsParticipant as $groupParticipant)
            {
                $group = $groupSet->where(['id' => $groupParticipant->training_group_id])->one();
                if ($group->branch_id === $participant->branch_id && date('Y-m-d', strtotime($group->finish_date . '+6 month')) >= $now)
                {
                    $flag = true;
                    break 2;
                }
            }
        }

        foreach ($err as $oneErr)
        {
            if ($flag)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && !$flag)
        {
            $this->foreign_event_id = $modelForeignEventID;
            $this->errors_id = 25;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }


    }

    private function CheckAchievement ($modelForeignEventID)
    {
        $err = ForeignEventErrorsWork::find()->where(['foreign_event_id' => $modelForeignEventID, 'time_the_end' => null, 'errors_id' => 26])->all();
        $achievementCount = count(ParticipantAchievementWork::find()->where(['foreign_event_id' => $modelForeignEventID])->all());

        foreach ($err as $oneErr)
        {
            if ($achievementCount !== 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $achievementCount === 0)
        {
            $this->foreign_event_id = $modelForeignEventID;
            $this->errors_id = 26;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckDoc ($modelForeignEventID, $foreignEvent)
    {
        $err = ForeignEventErrorsWork::find()->where(['foreign_event_id' => $modelForeignEventID, 'time_the_end' => null, 'errors_id' => 27])->all();

        foreach ($err as $oneErr)
        {
            if ($foreignEvent->docs_achievement !== NULL)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $foreignEvent->docs_achievement === NULL)
        {
            $this->foreign_event_id = $modelForeignEventID;
            $this->errors_id = 27;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckCompany ($modelForeignEventID, $foreignEvent)
    {
        $err = ForeignEventErrorsWork::find()->where(['foreign_event_id' => $modelForeignEventID, 'time_the_end' => null, 'errors_id' => 30])->all();

        foreach ($err as $oneErr)
        {
            if ($foreignEvent->company_id !== NULL)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $foreignEvent->company_id === NULL)
        {
            $this->foreign_event_id = $modelForeignEventID;
            $this->errors_id = 30;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckEventWay ($modelForeignEventID, $foreignEvent)
    {
        $err = ForeignEventErrorsWork::find()->where(['foreign_event_id' => $modelForeignEventID, 'time_the_end' => null, 'errors_id' => 31])->all();

        foreach ($err as $oneErr)
        {
            if ($foreignEvent->event_way_id !== NULL)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $foreignEvent->event_way_id === NULL)
        {
            $this->foreign_event_id = $modelForeignEventID;
            $this->errors_id = 31;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckErrorsForeignEvent ($modelForeignEventID)
    {
        $foreignEvent = ForeignEventWork::find()->where(['id' => $modelForeignEventID])->one();
        $participants = TeacherParticipantWork::find()->where(['foreign_event_id' => $modelForeignEventID])->all();

        $this->CheckDate($modelForeignEventID, $foreignEvent);
        $this->CheckCity($modelForeignEventID, $foreignEvent);
        $this->CheckParticipant($modelForeignEventID, $participants);
        $this->CheckParticipantGroup($modelForeignEventID, $foreignEvent, $participants);
        $this->CheckAchievement($modelForeignEventID);
        $this->CheckDoc($modelForeignEventID, $foreignEvent);
        $this->CheckCompany($modelForeignEventID, $foreignEvent);
        $this->CheckEventWay($modelForeignEventID, $foreignEvent);
    }

    public function CheckErrorsForeignEventWithoutAmnesty ($modelForeignEventID)
    {
        $this->NoAmnesty($modelForeignEventID);
        $this->CheckErrorsForeignEvent($modelForeignEventID);
    }
}
