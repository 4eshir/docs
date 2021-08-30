<?php


namespace app\models\extended;


use yii\base\Model;

class ResultReportModel extends Model
{
    public $result;

    public function rules()
    {
        return [
            [['result'], 'string'],
        ];
    }

    public function save()
    {

    }
}