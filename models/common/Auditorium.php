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
            [['is_education', 'branch_id'], 'integer'],
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
            'text' => 'Наименование',
            'files' => 'Файлы',
            'is_education' => 'Предназначен для обр. деят.',
            'branch_id' => 'Отдел',
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

    public function GetIsEducation()
    {
        return $this->is_education ? 'Да' : 'Нет';
    }

    public function GetBranchLink()
    {
        return Html::a($this->branch->name, \yii\helpers\Url::to(['branch/view', 'id' => $this->branch_id]));
    }

    public function uploadFiles($upd = null)
    {
        $path = '@app/upload/files/auds/';
        $result = '';
        $counter = 0;
        if (strlen($this->files) > 3)
            $counter = count(explode(" ", $this->files)) - 1;
        foreach ($this->filesList as $file) {
            $counter++;
            $filename = 'Файл'.$counter.'_'.$this->name.'_'.$this->id;
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = FileWizard::CutFilename($res);
            $res = mb_ereg_replace('[^а-яА-Я0-9a-zA-Z._]{1}', '', $res);
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->files = $result;
        else
            $this->files = $this->files.$result;
        return true;
    }
}
