<?php


namespace app\models\extended;
use yii\base\Model;

class Author extends Model
{
    public $id;
    public $program_id;

    public function rules()
    {
        return [
            [['id', 'program_id'], 'integer']
        ];
    }
}