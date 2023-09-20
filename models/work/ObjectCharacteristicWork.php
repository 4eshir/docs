<?php

namespace app\models\work;

use app\models\common\ObjectCharacteristic;
use app\models\components\FileWizard;
use app\models\null\MaterialObjectNull;
use app\models\null\CharacteristicObjectNull;
use Yii;
use yii\helpers\Html;


class ObjectCharacteristicWork extends ObjectCharacteristic
{
    public $documentFile; //документ основания

    public function rules()
    {
        return [
            [['documentFile'], 'file', 'extensions' => 'doc, docx, zip, rar, 7z, tag, pdf', 'skipOnEmpty' => true],
            [['material_object_id', 'characteristic_object_id'], 'required'],
            [['material_object_id', 'characteristic_object_id', 'integer_value', 'bool_value', 'dropdown_value'], 'integer'],
            [['double_value'], 'number'],
            [['date_value'], 'safe'],
            [['string_value', 'document_value'], 'string', 'max' => 1000],
            [['characteristic_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => CharacteristicObjectWork::className(), 'targetAttribute' => ['characteristic_object_id' => 'id']],
            [['material_object_id'], 'exist', 'skipOnError' => true, 'targetClass' => MaterialObjectWork::className(), 'targetAttribute' => ['material_object_id' => 'id']],
        ];
    }

	public function getMaterialObjectWork()
    {
        $try = $this->hasOne(MaterialObjectWork::className(), ['id' => 'material_object_id']);
        return $try->all() ? $try : new MaterialObjectNull();
    }

    public function getCharacteristicObjectWork()
    {
        $try = $this->hasOne(CharacteristicObjectWork::className(), ['id' => 'characteristic_object_id']);
        return $try->all() ? $try : new CharacteristicObjectNull();
    }

    public function getValue()
    {
    	if ($this->integer_value !== null) return $this->integer_value;
    	if ($this->double_value !== null) return $this->double_value;
    	if ($this->string_value !== null && strlen($this->string_value) > 0) return $this->string_value;
    	if ($this->bool_value !== null) return $this->bool_value;
    	if ($this->date_value !== null) return $this->date_value;
    	if ($this->document_value != null && strlen($this->document_value) > 0) return $this->document_value;
        if ($this->dropdown_value != null)
        {
            $result = DropdownCharacteristicObjectWork::find()->where(['id' => $this->dropdown_value])->one();
            return $result->item;
        }
    }

    /*--------------------------------------*/

    public function getDocumentLink()
    {
        return Html::a($this->document_value, \yii\helpers\Url::to(['material_object/get-file-characteristic', 'fileName' => $this->document_value, 'modelCharId' => $this->id]));
    }

    public function uploadDocument()
    {
        $path = '@app/upload/files/material-object/characteristic/';
        $filename = '';
        $filename = 'Док.'.$this->document_value.'_'.$this->id;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = FileWizard::CutFilename($res);
        $this->document_value = $res.'.'.$this->documentFile->extension;
        $this->documentFile->saveAs( $path.$res.'.'.$this->documentFile->extension);
    }
}
