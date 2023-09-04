<?php


namespace app\models;


use yii\db\ActiveRecord;

class Password extends ActiveRecord
{
    public $oldPass;
    public $newPass;

    public function rules()
    {
        return [
            [['oldPass', 'newPass'], 'string'],
        ];
    }
}