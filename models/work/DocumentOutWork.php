<?php

namespace app\models\common;

use app\models\components\FileWizard;
use Faker\Provider\File;
use Yii;
use yii\helpers\ArrayHelper;


class DocumentOutWork extends DocumentOut
{
    public $scanFile;
    public $docFiles;
    public $applicationFiles;
    public $signedString;
    public $executorString;
    public $registerString;
    public $positionCompany;

    public $isAnswer;

    public function rules()
    {
        return [
            [['scanFile'], 'file', 'extensions' => 'png, jpg, pdf, zip, rar, 7z, tag', 'skipOnEmpty' => true],
            [['docFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'xls, xlsx, doc, docx, zip, rar, 7z, tag', 'maxFiles' => 10],
            [['applicationFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'ppt, pptx, xls, xlsx, png, jpg, pdf, doc, docx, zip, rar, 7z, tag', 'maxFiles' => 10],
            [['positionCompany'], 'safe'],
            [['signedString', 'executorString', 'registerString', 'key_words'], 'string', 'message' => 'Введите корректные ФИО'],
            [['document_name', 'document_date', 'document_theme', 'signed_id', 'executor_id', 'send_method_id', 'sent_date', 'register_id', 'document_number', 'signedString', 'executorString'], 'required'],
            [['document_date', 'sent_date', 'isAnswer'], 'safe'],
            [['company_id', 'position_id', 'signed_id', 'executor_id', 'send_method_id', 'register_id', 'document_postfix', 'document_number'], 'integer'],
            [['document_theme', 'Scan', 'key_words', 'isAnswer'], 'string', 'max' => 1000],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::className(), 'targetAttribute' => ['position_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['executor_id' => 'id']],
            [['register_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['register_id' => 'id']],
            [['send_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => SendMethod::className(), 'targetAttribute' => ['send_method_id' => 'id']],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['signed_id' => 'id']],
            [['correspondent_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['correspondent_id' => 'id']],
        ];
    }

    public function getImagesLinks()
    {
        $path = ArrayHelper::getColumn(self::find()->all(), Yii::$app->basePath.'/upload/files/'.$this->Scan);
        return $path;
    }

    public function getPositionCompany()
    {
        return $this->position->name.' '.$this->company->name;
    }

    public function getCompanyName()
    {
        return $this->company->name;
    }

    public function getPositionName()
    {
        return $this->position->name;
    }

    public function getIsAnswer()
    {
        return $this->isAnswer;
    }

    public function uploadScanFile()
    {
        $path = '@app/upload/files/document_out/scan/';
        $date = $this->document_date;
        $new_date = '';
        for ($i = 0; $i < strlen($date); ++$i)
            if ($date[$i] != '-')
                $new_date = $new_date.$date[$i];
        $filename = 'Исх.'.$new_date.'_'.$this->document_number.'_'.$this->company->name.'_'.$this->document_theme;
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $res = FileWizard::CutFilename($res);
        $this->Scan = $res.'.'.$this->scanFile->extension;
        $this->scanFile->saveAs( $path.$res.'.'.$this->scanFile->extension);
    }

    public function uploadApplicationFiles($upd = null)
    {
        $path = '@app/upload/files/document_out/apps/';
        $result = '';
        $counter = 0;
        if (strlen($this->doc) > 4)
            $counter = count(explode(" ", $this->applications)) - 1;
        foreach ($this->applicationFiles as $file) {
            $counter++;
            $date = $this->document_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            $filename = '';
            if ($this->company->short_name !== '')
            {
                $filename = 'Приложение'.$counter.'_Исх.'.$new_date.'_'.$this->document_number.'_'.$this->company->short_name.'_'.$this->document_theme;
            }
            else
            {
                $filename = 'Приложение'.$counter.'_Исх.'.$new_date.'_'.$this->document_number.'_'.$this->company->name.'_'.$this->document_theme;
            }
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
            $res = FileWizard::CutFilename($res);
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->applications = $result;
        else
            $this->applications = $this->applications.$result;
        return true;
    }

    public function uploadDocFiles($upd = null)
    {
        $path = '@app/upload/files/document_out/docs/';
        $result = '';
        $counter = 0;
        if (strlen($this->doc) > 4)
            $counter = count(explode(" ", $this->doc)) - 1;
        foreach ($this->docFiles as $file) {
            $counter++;
            $date = $this->document_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            $filename = '';
            if ($this->company->short_name !== '')
            {
                $filename = $counter.'_Исх.'.$new_date.'_'.$this->document_number.'_'.$this->company->short_name.'_'.$this->document_theme;
            }
            else
            {
                $filename = $counter.'_Исх.'.$new_date.'_'.$this->document_number.'_'.$this->company->name.'_'.$this->document_theme;
            }
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
            $res = FileWizard::CutFilename($res);
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->doc = $result;
        else
            $this->doc = $this->doc.$result;
        return true;
    }

    public function getDocumentNumber()
    {
        $docs = DocumentOut::find()->orderBy(['document_number' => SORT_ASC, 'document_postfix' => SORT_ASC])->all();
        if (end($docs)->document_date > $this->document_date && $this->document_theme != 'Резерв')
        {
            $tempId = 0;
            $tempPre = 0;
            if (count($docs) == 0)
                $tempId = 1;
            for ($i = count($docs) - 1; $i >= 0; $i--)
            {
                if ($docs[$i]->document_date <= $this->document_date)
                {
                    $tempId = $docs[$i]->document_number;
                    if ($docs[$i]->document_postfix != null)
                        $tempPre = $docs[$i]->document_postfix + 1;
                    else
                        $tempPre = 1;
                    break;
                }
            }

            $this->document_number = $tempId;
            $this->document_postfix = $tempPre;
            Yii::$app->session->addFlash('warning', 'Добавленный документ должен был быть зарегистрирован раньше. Номер документа: '.$this->document_number.'/'.$this->document_postfix);
        }
        else
        {
            if (count($docs) == 0)
                $this->document_number = 1;
            else
            {
                $this->document_number = end($docs)->document_number + 1;
            }
        }
    }

    public function beforeSave($insert)
    {
        if ($this->correspondent_id != null)
        {
            $this->company_id = $this->correspondent->company_id;
            $this->position_id = $this->correspondent->position_id;
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }

    public function afterSave($insert, $changedAttributes)
    {
        parent::afterSave($insert, $changedAttributes); // TODO: Change the autogenerated stub
        if ($this->isAnswer !== "")
        {
            $inoutdocs = InOutDocs::find()->where(['id' => $this->isAnswer])->one();
            $inoutdocsYet = InOutDocs::find()->where(['document_out_id' => $this->id])->one();
            if ($inoutdocsYet->id == $inoutdocs->id)
                return;
            if ($inoutdocs !== null)
            {
                if ($inoutdocsYet !== null)
                {
                    $inoutdocsYet->document_out_id = null;
                    $docIn = DocumentIn::find()->where(['id' => $inoutdocsYet->document_in_id])->one();
                    $docIn->needAnswer = 1;
                    $docIn->save(false);
                    $inoutdocsYet->save();
                }
                $inoutdocs->document_out_id = $this->id;
                $inoutdocs->save();
            }


            $docIn = DocumentIn::find()->where(['id' => $inoutdocs->document_in_id])->one();
            if ($docIn !== null)
            {
                $docIn->needAnswer = 0;
                $docIn->save(false);
            }

        }
        else
        {
            $inoutdocs = InOutDocs::find()->where(['document_out_id' => $this->id])->one();
            if ($inoutdocs !== null)
            {
                $inoutdocs->document_out_id = null;
                $inoutdocs->save();
            }

        }
    }

    public function beforeDelete()
    {
        $links = InOutDocs::find()->where(['document_out_id' => $this->id])->all();
        foreach ($links as $linkOne)
        {
            $docIn = DocumentIn::find()->where(['id' => $linkOne->document_in_id])->one();
            $docIn->needAnswer = 1;
            $docIn->save();
            $linkOne->document_out_id = null;
            $linkOne->save();
        }
        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }
}