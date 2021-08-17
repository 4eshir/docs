<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "program_errors".
 *
 * @property int $id
 * @property int $training_program_id
 * @property int $errors_id
 * @property string $time_start
 * @property string|null $time_the_end
 */
class ProgramErrors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'program_errors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['training_program_id', 'errors_id', 'time_start'], 'required'],
            [['training_program_id', 'errors_id'], 'integer'],
            [['time_start', 'time_the_end'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'training_program_id' => 'Training Program ID',
            'errors_id' => 'Errors ID',
            'time_start' => 'Time Start',
            'time_the_end' => 'Time The End',
        ];
    }
}
