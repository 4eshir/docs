<?php

namespace app\models\work;

use app\models\common\InOutDocs;
use Yii;


class InOutDocsWork extends InOutDocs
{
    public function getDocInName()
    {
        return 'Входящий документ "'.$this->documentIn->document_theme.'"';
    }
}
