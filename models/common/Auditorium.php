<?php

namespace app\models\common;

use app\models\components\FileWizard;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "auditorium".
 *
 * @property int $id
 * @property string $name
 * @property float $square
 * @property string|null $text
 * @property int|null $capacity
 * @property string|null $files
 * @property int $is_education
 * @property int $branch_id
 *
 * @property Branch $branch
 * @property TrainingGroupLesson[] $trainingGroupLessons
 */
class Auditorium extends \yii\db\ActiveRecord
{
    public $filesList;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'auditorium';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'square', 'branch_id'], 'required'],
            [['square'], 'number'],
            [['is_education', 'branch_id', 'capacity'], 'integer'],
            [['name', 'text', 'files'], 'string', 'max' => 1000],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['filesList'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Уникальный глобальный номер',
            'square' => 'Площадь (кв.м.)',
            'text' => 'Имя',
            'files' => 'Файлы',
            'is_education' => 'Предназначено для обр. деят.',
            'capacity' => 'Кол-во ученико-мест',
            'branch_id' => 'Отдел',
            'filesList' => 'Файлы',
        ];
    }

    /**
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    /**
     * Gets query for [[TrainingGroupLessons]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTrainingGroupLessons()
    {
        return $this->hasMany(TrainingGroupLesson::className(), ['auditorium_id' => 'id']);
    }

}
