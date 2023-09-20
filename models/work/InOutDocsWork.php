<?php

namespace app\models\work;

use app\models\common\InOutDocs;
use app\models\null\PeopleNull;
use Yii;


class InOutDocsWork extends InOutDocs
{
    public function getDocInName()
    {
        return 'Входящий документ ('.$this->documentIn->real_date.' №'.$this->documentIn->real_number.') "'.$this->documentIn->document_theme.'"';
    }

    public function getPeopleWork()
    {
        $try = $this->hasOne(PeopleWork::className(), ['id' => 'people_id']);
        return $try->all() ? $try : new PeopleNull();
    }
}
