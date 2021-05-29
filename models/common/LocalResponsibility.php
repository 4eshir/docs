<?php

namespace app\models\common;

use app\models\components\FileWizard;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "local_responsibility".
 *
 * @property int $id
 * @property int $responsibility_type_id
 * @property int|null $branch_id
 * @property int|null $auditorium_id
 * @property int|null $people_id
 * @property int|null $regulation_id
 * @property string|null $files
 *
 * @property Auditorium $auditorium
 * @property Branch $branch
 * @property People $people
 * @property Regulation $regulation
 * @property ResponsibilityType $responsibilityType
 */
class LocalResponsibility extends \yii\db\ActiveRecord
{
    public $filesStr;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'local_responsibility';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['responsibility_type_id'], 'required'],
            [['responsibility_type_id', 'branch_id', 'auditorium_id', 'people_id', 'regulation_id'], 'integer'],
            [['files'], 'string', 'max' => 1000],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::className(), 'targetAttribute' => ['auditorium_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['people_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['people_id' => 'id']],
            [['regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regulation::className(), 'targetAttribute' => ['regulation_id' => 'id']],
            [['responsibility_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResponsibilityType::className(), 'targetAttribute' => ['responsibility_type_id' => 'id']],
            [['filesStr'], 'file', 'skipOnEmpty' => true, 'maxFiles' => 10],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'responsibility_type_id' => 'Вид ответственности',
            'responsibilityTypeStr' => 'Вид ответственности',
            'branch_id' => 'Отдел',
            'branchStr' => 'Отдел',
            'auditorium_id' => 'Помещение',
            'auditoriumStr' => 'Помещение',
            'people_id' => 'Работник',
            'peopleStr' => 'Работник',
            'regulation_id' => 'Положение/инструкция',
            'regulationStr' => 'Положение/инструкция',
            'files' => 'Файлы',
            'filesStr' => 'Файлы',
        ];
    }

    /**
     * Gets query for [[Auditorium]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditorium()
    {
        return $this->hasOne(Auditorium::className(), ['id' => 'auditorium_id']);
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
     * Gets query for [[People]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasOne(People::className(), ['id' => 'people_id']);
    }

    /**
     * Gets query for [[Regulation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegulation()
    {
        return $this->hasOne(Regulation::className(), ['id' => 'regulation_id']);
    }

    /**
     * Gets query for [[ResponsibilityType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsibilityType()
    {
        return $this->hasOne(ResponsibilityType::className(), ['id' => 'responsibility_type_id']);
    }

    public function getResponsibilityTypeStr()
    {
        return Html::a($this->responsibilityType->name, \yii\helpers\Url::to(['responsibility-type/view', 'id' => $this->responsibility_type_id]));
    }

    public function getBranchStr()
    {
        return Html::a($this->branch->name, \yii\helpers\Url::to(['branch/view', 'id' => $this->branch_id]));
    }

    public function getAuditoriumStr()
    {
        return Html::a($this->auditorium->name, \yii\helpers\Url::to(['auditorium/view', 'id' => $this->auditorium_id]));
    }

    public function getPeopleStr()
    {
        return Html::a($this->people->fullName, \yii\helpers\Url::to(['people/view', 'id' => $this->people_id]));
    }


    public function getRegulationStr()
    {
        return Html::a($this->regulation->name, \yii\helpers\Url::to(['regulation/view', 'id' => $this->regulation_id]));
    }

    public function uploadFiles($upd = null)
    {
        $path = '@app/upload/files/responsibility/';
        $result = '';
        $counter = 0;
        if (strlen($this->files) > 3)
            $counter = count(explode(" ", $this->files)) - 1;
        foreach ($this->filesStr as $file) {
            $counter++;
            $filename = 'Файл'.$counter.'_'.$this->id.'_'.$this->people->secondname.'_'.$this->responsibilityType->name;
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
