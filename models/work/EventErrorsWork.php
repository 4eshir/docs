<?php

namespace app\models\work;

use app\models\common\EventErrors;
use Yii;


class EventErrorsWork extends EventErrors
{
    public function EventAmnesty ($modelEventID)
    {
        $errors = EventErrorsWork::find()->where(['event_id' => $modelEventID, 'time_the_end' => null, 'amnesty' => null])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = 1;
            $err->save();
        }
    }

    private function NoAmnesty ($modelEventID)
    {
        $errors = EventErrorsWork::find()->where(['event_id' => $modelEventID, 'time_the_end' => null, 'amnesty' => 1])->all();
        foreach ($errors as $err)
        {
            $err->amnesty = null;
            $err->save();
        }
    }

    private function CheckDate ($modelEventID, $event)
    {
        $err = EventErrorsWork::find()->where(['event_id' => $modelEventID, 'time_the_end' => null, 'errors_id' => 22])->all();

        foreach ($err as $oneErr)
        {
            if ($event->start_date <= $event->finish_date)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $event->start_date > $event->finish_date)
        {
            $this->event_id = $modelEventID;
            $this->errors_id = 22;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckParticipant ($modelEventID, $event)
    {
        $err = EventErrorsWork::find()->where(['event_id' => $modelEventID, 'time_the_end' => null, 'errors_id' => 24])->all();
        $participantCount = $event->participants_count;

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
            $this->event_id = $modelEventID;
            $this->errors_id = 24;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckBranch ($modelEventID)
    {
        $err = EventErrorsWork::find()->where(['event_id' => $modelEventID, 'time_the_end' => null, 'errors_id' => 32])->all();
        $branchCount = count(EventBranchWork::find()->where(['event_id' => $modelEventID])->all());

        foreach ($err as $oneErr)
        {
            if ($branchCount !== 0)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $branchCount === 0)
        {
            $this->event_id = $modelEventID;
            $this->errors_id = 32;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckOrder ($modelEventID, $event)
    {
        $err = EventErrorsWork::find()->where(['event_id' => $modelEventID, 'time_the_end' => null, 'errors_id' => 33])->all();

        foreach ($err as $oneErr)
        {
            if ($event->order_id !== NULL)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $event->order_id === NULL)
        {
            $this->event_id = $modelEventID;
            $this->errors_id = 33;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckPhoto ($modelEventID, $event)
    {
        $err = EventErrorsWork::find()->where(['event_id' => $modelEventID, 'time_the_end' => null, 'errors_id' => 34])->all();

        foreach ($err as $oneErr)
        {
            if ($event->photos !== '')     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $event->photos === '')
        {
            $this->event_id = $modelEventID;
            $this->errors_id = 34;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckKeyWord ($modelEventID, $event)
    {
        $err = EventErrorsWork::find()->where(['event_id' => $modelEventID, 'time_the_end' => null, 'errors_id' => 35])->all();

        foreach ($err as $oneErr)
        {
            if ($event->key_words !== '')     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $event->key_words === '')
        {
            $this->event_id = $modelEventID;
            $this->errors_id = 35;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    private function CheckWay ($modelEventID, $event)
    {
        $err = EventErrorsWork::find()->where(['event_id' => $modelEventID, 'time_the_end' => null, 'errors_id' => 31])->all();

        foreach ($err as $oneErr)
        {
            if ($event->event_way_id !== NULL)     // ошибка исправлена
            {
                $oneErr->time_the_end = date("Y.m.d H:i:s");
                $oneErr->save();
            }
        }

        if (count($err) === 0 && $event->event_way_id === NULL)
        {
            $this->event_id = $modelEventID;
            $this->errors_id = 31;
            $this->time_start = date("Y.m.d H:i:s");
            $this->save();
        }
    }

    public function CheckErrorsEvent ($modelEventID)
    {
        $event = EventWork::find()->where(['id' => $modelEventID])->one();

        $this->CheckDate($modelEventID, $event);
        $this->CheckParticipant($modelEventID, $event);
        $this->CheckBranch($modelEventID);
        $this->CheckOrder($modelEventID, $event);
        $this->CheckPhoto($modelEventID, $event);
        $this->CheckKeyWord($modelEventID, $event);
        $this->CheckWay($modelEventID, $event);
    }

    public function CheckErrorsEventWithoutAmnesty ($modelEventID)
    {
        $this->NoAmnesty($modelEventID);
        $this->CheckErrorsEvent($modelEventID);
    }
}
