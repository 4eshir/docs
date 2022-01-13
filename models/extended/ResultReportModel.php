<?php


namespace app\models\extended;


use yii\base\Model;

class ResultReportModel extends Model
{
    public $header;

    public $result;

    public $debugInfo;
    public $debugInfo2;

    public function rules()
    {
        return [
            [['result', 'debugInfo', 'debugInfo2'], 'string'],
        ];
    }

    public function save()
    {

    }
}