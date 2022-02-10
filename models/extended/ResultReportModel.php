<?php


namespace app\models\extended;


use yii\base\Model;

class ResultReportModel extends Model
{
    public $header;

    public $result;

    public $debugInfo; //вывод кол-ва обучающихся
    public $debugInfo2; //вывод человеко-часов
    public $debugInfo3; //вывод данных по мероприятиям

    public function rules()
    {
        return [
            [['result', 'debugInfo', 'debugInfo2', 'debugInfo3'], 'string'],
        ];
    }

    public function save()
    {

    }
}