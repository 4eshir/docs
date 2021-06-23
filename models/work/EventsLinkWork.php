<?php

namespace app\models\common;

use Yii;


class EventsLinkWork extends EventsLink
{
    public $eventExternalName;


    public function rules()
    {
        return [
            [['event_external_id', 'event_id'], 'required'],
            [['event_external_id', 'event_id'], 'integer'],
            [['eventExternalName'], 'string'],
        ];
    }
}
