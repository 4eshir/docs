<?php


namespace app\models\extended;


use yii\base\Model;

class TeamModel extends Model
{
    public $check;

    public function rules()
    {
        return [
            [['check'], 'integer'],
        ];
    }
}