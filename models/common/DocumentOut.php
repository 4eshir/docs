<?php

namespace app\models\common;

use app\models\components\FileWizard;
use Faker\Provider\File;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "document_out".
 *
 * @property int $id
 * @property int $document_number
 * @property int $document_postfix
 * @property string $document_date
 * @property string $document_name
 * @property string $document_theme
 * @property int $correspondent_id
 * @property int $company_id
 * @property int $position_id
 * @property int $signed_id
 * @property int $executor_id
 * @property int $send_method_id
 * @property string $sent_date
 * @property string $Scan
 * @property string $doc
 * @property string $applications
 * @property int $register_id
 * @property string $key_words
 *
 * @property People $executor
 * @property People $register
 * @property People $correspondent
 * @property SendMethod $sendMethod
 * @property People $signed
 */
class DocumentOut extends \yii\db\ActiveRecord
{
    public $scanFile;
    public $docFiles;
    public $applicationFiles;
    public $signedString;
    public $executorString;
    public $registerString;
    public $positionCompany;

    public $isAnswer;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_out';
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'document_number' => 'Номер документа',
            'document_date' => 'Дата документа',
            'document_theme' => 'Тема документа',
            'company_id' => 'Организация',
            'position_id' => 'Должность',
            'signed_id' => 'Кем подписан',
            'executor_id' => 'Кто исполнил',
            'send_method_id' => 'Способ отправки',
            'sent_date' => 'Дата отправки',
            'Scan' => 'Скан',
            'applications' => 'Приложения',
            'register_id' => 'Кто зарегистрировал',
            'key_words' => 'Ключевые слова',
        ];
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'company_id']);
    }

    /**
     * Gets query for [[Position]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPosition()
    {
        return $this->hasOne(Position::className(), ['id' => 'position_id']);
    }

    /**
     * Gets query for [[Executor]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getExecutor()
    {
        return $this->hasOne(People::className(), ['id' => 'executor_id']);
    }

    /**
     * Gets query for [[Register]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegister()
    {
        return $this->hasOne(User::className(), ['id' => 'register_id']);
    }

    /**
     * Gets query for [[SendMethod]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSendMethod()
    {
        return $this->hasOne(SendMethod::className(), ['id' => 'send_method_id']);
    }

    /**
     * Gets query for [[Signed]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSigned()
    {
        return $this->hasOne(People::className(), ['id' => 'signed_id']);
    }

    /**
     * Gets query for [[Correspondent]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCorrespondent()
    {
        return $this->hasOne(People::className(), ['id' => 'correspondent_id']);
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
        /*$max = DocumentOut::find()->max('document_number');
        if ($max == null)
            $max = 1;
        else
            $max = $max + 1;
        return $max;*/
    }

    public function beforeSave($insert)
    {
        /*$fioSigned = explode(" ", $this->signedString);
        $fioExecutor = explode(" ", $this->executorString);
        $fioRegister = explode(" ", $this->registerString);

        $fioSignedDb = People::find()->where(['secondname' => $fioSigned[0]])
            ->andWhere(['firstname' => $fioSigned[1]])
            ->andWhere(['patronymic' => $fioSigned[2]])->one();
        $fioExecutorDb = People::find()->where(['secondname' => $fioExecutor[0]])
            ->andWhere(['firstname' => $fioExecutor[1]])
            ->andWhere(['patronymic' => $fioExecutor[2]])->one();
        $fioRegisterDb = User::find()->where(['secondname' => $fioRegister[0]])
            ->andWhere(['firstname' => $fioRegister[1]])
            ->andWhere(['patronymic' => $fioRegister[2]])->one();

        if ($fioSignedDb !== null)
            $this->signed_id = $fioSignedDb->id;

        if ($fioExecutorDb !== null)
            $this->executor_id = $fioExecutorDb->id;

        if ($fioRegisterDb !== null)
            $this->register_id = $fioRegisterDb->id;
        */
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

    /*public function getImagesLinksData()
    {
        $files = UploadsFiles::find()->all();
        return ArrayHelper::toArray($files,[
            UploadsFiles::class => [
                'caption' => 'file',
                'key' => 'id'
            ]
        ]);
    }*/
}
