<?php

namespace app\models\work;

use app\models\common\InOutDocs;
use Yii;


class InOutDocsWork extends InOutDocs
{
    public function getDocInName()
    {
        return 'Входящий документ ('.$this->documentIn->real_date.' №'.$this->documentIn->real_number.') "'.$this->documentIn->document_theme.'"';
    }

    public function getPeopleWork()
    {
        return $this->hasOne(PeopleWork::className(), ['id' => 'people_id']);
    }
}
