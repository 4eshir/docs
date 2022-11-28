<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "patchnotes".
 *
 * @property int $id
 * @property int $first_number
 * @property int $second_number
 * @property string $date
 * @property string $text
 */
class Patchnotes extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'patchnotes';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_number', 'second_number', 'date', 'text'], 'required'],
            [['first_number', 'second_number'], 'integer'],
            [['date'], 'safe'],
            [['text'], 'string', 'max' => 10000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_number' => 'First Number',
            'second_number' => 'Second Number',
            'date' => 'Date',
            'text' => 'Text',
        ];
    }
}
