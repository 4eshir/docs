<?php


namespace app\models\components;


use app\models\common\Log;

class Logger
{
    public static function WriteLog($user_id, $log_text)
    {
        $model = new Log();
        $model->user_id = $user_id;
        $model->text = $log_text;
        $model->date = date('Y-m-d');
        $model->time = date('H:i:s');
        $model->save();
    }
}