<?php


namespace app\models\extended;


use yii\base\Model;

class ResultReportModel extends Model
{
    public $result;

    public $debugInfo;

    public function rules()
    {
        return [
            [['result', 'debugInfo'], 'string'],
        ];
    }

    public function save()
    {

    }
}