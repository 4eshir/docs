<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "document_in".
 *
 * @property int $id
 * @property int $local_number
 * @property int $local_postfix
 * @property string $local_date
 * @property string $real_number
 * @property string $real_date
 * @property int $correspondent_id
 * @property int $position_id
 * @property int $company_id
 * @property string $document_theme
 * @property int $signed_id
 * @property string $target
 * @property int $get_id
 * @property int $send_method_id
 * @property string $scan
 * @property string $doc
 * @property string $applications
 * @property int $register_id
 * @property string $key_words
 *
 * @property Company $company
 * @property User $get
 * @property Position $position
 * @property User $register
 * @property People $signed
 * @property SendMethod $sendMethod
 * @property People $correspondent
 */
class DocumentIn extends \yii\db\ActiveRecord
{
    public $signedString;
    public $getString;

    public $scanFile;
    public $docFiles;
    public $applicationFiles;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_in';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['scanFile'], 'file', 'extensions' => 'png, jpg, pdf', 'skipOnEmpty' => true],
            [['docFiles'], 'file', 'extensions' => 'xls, xlsx, doc, docx', 'skipOnEmpty' => true, 'maxFiles' => 10],
            [['applicationFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf, png, jpg, doc, docx', 'maxFiles' => 10],

            [['signedString', 'getString'], 'string', 'message' => 'Введите корректные ФИО'],
            [['local_date', 'real_number', 'real_date', 'send_method_id', 'position_id', 'company_id', 'document_theme', 'signed_id', 'target', 'get_id', 'register_id'], 'required'],
            [['local_number', 'position_id', 'company_id', 'signed_id', 'get_id', 'register_id', 'correspondent_id', 'local_postfix'], 'integer'],
            [['local_date', 'real_date'], 'safe'],
            [['document_theme', 'target', 'scan', 'applications', 'key_words', 'real_number'], 'string', 'max' => 1000],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['get_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['get_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::className(), 'targetAttribute' => ['position_id' => 'id']],
            [['register_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['register_id' => 'id']],
            [['signed_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['signed_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'local_number' => '№ п/п',
            'local_date' => 'Дата поступления документа',
            'real_number' => 'Регистрационный номер входящего документа ',
            'real_date' => 'Дата входящего документа ',
            'position_id' => 'Должность',
            'company_id' => 'Организация',
            'document_theme' => 'Тема документа',
            'signed_id' => 'Кем подписан',
            'target' => 'Кому адресован',
            'get_id' => 'Кем получен',
            'scan' => 'Скан',
            'applications' => 'Приложения',
            'register_id' => 'Регистратор документа',
            'key_words' => 'Ключевые слова'
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
     * Gets query for [[Get]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGet()
    {
        return $this->hasOne(User::className(), ['id' => 'get_id']);
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
     * Gets query for [[Register]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegister()
    {
        return $this->hasOne(User::className(), ['id' => 'register_id']);
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
     * Gets query for [[SendMethod]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSendMethod()
    {
        return $this->hasOne(SendMethod::className(), ['id' => 'send_method_id']);
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
    
    //-----------------------------------

    public function uploadScanFile()
    {
        $path = '@app/upload/files/document_in/scan/';
        $date = $this->local_date;
        $new_date = '';
        $filename = '';
        for ($i = 0; $i < strlen($date); ++$i)
            if ($date[$i] != '-')
                $new_date = $new_date.$date[$i];
        if ($this->company->short_name !== '')
        {
            $filename = 'Вх.'.$new_date.'_'.$this->local_number.'_'.$this->company->short_name.'_'.$this->document_theme;
        }
        else
        {
            $filename = 'Вх.'.$new_date.'_'.$this->local_number.'_'.$this->company->name.'_'.$this->document_theme;
        }
        $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
        $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
        $this->scan = $res.'.'.$this->scanFile->extension;
        $this->scanFile->saveAs( $path.$res.'.'.$this->scanFile->extension);
    }

    public function uploadApplicationFiles($upd = null)
    {
        $path = '@app/upload/files/document_in/apps/';
        $result = '';
        $counter = 0;
        foreach ($this->applicationFiles as $file) {
            $counter++;
            $date = $this->local_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            if ($this->company->short_name !== '')
            {
                $filename = 'Приложение'.$counter.'_Вх.'.$new_date.'_'.$this->local_number.'_'.$this->company->short_name.'_'.$this->document_theme;
            }
            else
            {
                $filename = 'Приложение'.$counter.'_Вх.'.$new_date.'_'.$this->local_number.'_'.$this->company->name.'_'.$this->document_theme;
            }
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
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
        $path = '@app/upload/files/document_in/docs/';
        $result = '';
        $counter = 0;
        foreach ($this->docFiles as $file) {
            $counter++;
            $date = $this->local_date;
            $new_date = '';
            for ($i = 0; $i < strlen($date); ++$i)
                if ($date[$i] != '-')
                    $new_date = $new_date.$date[$i];
            if ($this->company->short_name !== '')
            {
                $filename = 'Ред'.$counter.'_Вх.'.$new_date.'_'.$this->local_number.'_'.$this->company->short_name.'_'.$this->document_theme;
            }
            else
            {
                $filename = 'Ред'.$counter.'_Вх.'.$new_date.'_'.$this->local_number.'_'.$this->company->name.'_'.$this->document_theme;
            }
            $res = mb_ereg_replace('[ ]{1,}', '_', $filename);
            $res = mb_ereg_replace('[^а-яА-Я0-9._]{1}', '', $res);
            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->doc = $result;
        else
            $this->doc = $this->doc.$result;
        return true;
    }

    //-----------------------------------

    public function beforeSave($insert)
    {
        $fioSigned = explode(" ", $this->signedString);
        $fioGet = explode(" ", $this->getString);

        $fioSignedDb = People::find()->where(['secondname' => $fioSigned[0]])
            ->andWhere(['firstname' => $fioSigned[1]])
            ->andWhere(['patronymic' => $fioSigned[2]])->one();
        $fioGetDb = User::find()->where(['secondname' => $fioGet[0]])
            ->andWhere(['firstname' => $fioGet[1]])
            ->andWhere(['patronymic' => $fioGet[2]])->one();
        if ($fioSignedDb !== null)
            $this->signed_id = $fioSignedDb->id;

        if ($fioGetDb !== null)
            $this->get_id = $fioGetDb->id;

        $this->register_id = Yii::$app->user->identity->getId();

        if ($this->correspondent_id != null)
        {
            $this->company_id = $this->correspondent->company_id;
            $this->position_id = $this->correspondent->position_id;
        }

        return parent::beforeSave($insert); // TODO: Change the autogenerated stub
    }


    public function getDocumentNumber()
    {
        $docs = DocumentIn::find()->orderBy(['local_number' => SORT_ASC, 'local_postfix' => SORT_ASC])->all();
        if (end($docs)->local_date > $this->local_date && $this->document_theme != 'Резерв')
        {
            $tempId = 0;
            $tempPre = 0;
            if (count($docs) == 0)
                $tempId = 1;
            for ($i = count($docs) - 1; $i >= 0; $i--)
            {
                if ($docs[$i]->local_date <= $this->local_date)
                {
                    $tempId = $docs[$i]->local_number;
                    if ($docs[$i]->local_postfix != null)
                        $tempPre = $docs[$i]->local_postfix + 1;
                    else
                        $tempPre = 1;
                    break;
                }
            }

            $this->local_number = $tempId;
            $this->local_postfix = $tempPre;
            Yii::$app->session->addFlash('warning', 'Добавленный документ должен был быть зарегистрирован раньше. Номер документа: '.$this->local_number.'/'.$this->local_postfix);
        }
        else
        {
            if (count($docs) == 0)
                $this->local_number = 1;
            else
            {
                $this->local_number = end($docs)->local_number + 1;
            }
        }
        /*$max = DocumentOut::find()->max('document_number');
        if ($max == null)
            $max = 1;
        else
            $max = $max + 1;
        return $max;*/
    }
}