<?php

namespace app\models\common;

use Yii;


class InOutDocsWork extends InOutDocs
{
    public function getDocInName()
    {
        return 'Входящий документ "'.$this->documentIn->document_theme.'"';
    }
}
