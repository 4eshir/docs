<?php


namespace app\models;
use app\models\common\User;
use yii\db\ActiveRecord;

class ForgotPassword extends ActiveRecord
{
    public $email;

    public function rules()
    {
        return [
            ['email', 'string'],
            ['email', 'validateEmail'],
        ];
    }

    public function validateEmail()
    {
        $email = User::find()->where(['username' => $this->email])->all();
        return count($email) > 0;
    }
}