<?php

namespace app\models\common;

use app\models\components\FileWizard;
use Yii;

/**
 * This is the model class for table "material_object".
 *
 * @property int $id
 * @property string $unique_id
 * @property string $name
 * @property string $acceptance_date
 * @property float $balance_price
 * @property int $count
 * @property int $main
 * @property string|null $files
 *
 * @property PeopleMaterialObject[] $peopleMaterialObjects
 * @property TemporaryJournal[] $temporaryJournals
 */
class MaterialObject extends \yii\db\ActiveRecord
{
    public $upFiles;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'material_object';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['unique_id', 'name', 'acceptance_date', 'balance_price', 'count', 'main'], 'required'],
            [['upFiles'], 'file', 'extensions' => 'jpg, png, pdf, doc, docx, zip, rar, 7z, tag', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['acceptance_date'], 'safe'],
            [['balance_price'], 'number'],
            [['count', 'main'], 'integer'],
            [['unique_id', 'name', 'files'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'unique_id' => 'Уникальный идентификатор',
            'name' => 'Наименование',
            'acceptance_date' => 'Дата постановки на учет',
            'balance_price' => 'Балансовая стоимость',
            'count' => 'Количество',
            'main' => 'Основной',
            'files' => 'Файлы',
            'upFiles' => 'Файлы',
        ];
    }

    /**
     * Gets query for [[PeopleMaterialObjects]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeopleMaterialObjects()
    {
        return $this->hasMany(PeopleMaterialObject::className(), ['material_object_id' => 'id']);
    }

    /**
     * Gets query for [[TemporaryJournals]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTemporaryJournals()
    {
        return $this->hasMany(TemporaryJournal::className(), ['material_object_id' => 'id']);
    }

    public function uploadUpFiles($upd = null)
    {
        $path = '@app/upload/files/material-object/';
        $counter = 0;
        if (strlen($this->files) > 3)
            $counter = count(explode(" ", $this->files)) - 1;
        foreach ($this->upFiles as $file) {
            $counter++;
            $date = $this->acceptance_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];

            $filename = 'Ф'.$counter.'_'.$new_date.'_'.$this->unique_id.'-'.$this->name;
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
