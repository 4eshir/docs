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
            [['applicationFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, pdf, doc, docx', 'maxFiles' => 10,'checkExtensionByMimeType'=>false],

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
            'local_number' => 'Local Number',
            'local_date' => 'Local Date',
            'real_number' => 'Real Number',
            'real_date' => 'Real Date',
            'position_id' => 'Position ID',
            'company_id' => 'Company ID',
            'document_theme' => 'Document Theme',
            'signed_id' => 'Signed ID',
            'target' => 'Target',
            'get_id' => 'Get ID',
            'scan' => 'Scan',
            'applications' => 'Applications',
            'register_id' => 'Register ID',
            'key_words' => 'Key Words'
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
            $res = '';
            $sn = $this->company->short_name;
            for ($i = 0; $i < strlen($sn); $i++)
                if ($sn[$i] == ' ')
                    $res= $res.'_';
                else
                    $res = $res.$sn[$i];
            $filename = 'Вх.'.$new_date.'_'.$this->local_number.'_'.$res.'_'.$this->document_theme;
        }
        else
        {
            $res = '';
            $sn = $this->company->name;
            for ($i = 0; $i < strlen($sn); $i++)
                if ($sn[$i] == ' ')
                    $res= $res.'_';
                else
                    $res = $res.$sn[$i];
            $filename = 'Вх.'.$new_date.'_'.$this->local_number.'_'.$res.'_'.$this->document_theme;
        }
        $newFilename = $filename;
        $res = '';
        for ($i = 0; $i < strlen($newFilename); $i++)
        {
            if ($newFilename[$i] == ' ')
                $res= $res.'_';
            else if ($newFilename[$i] == '"' || $newFilename[$i] == '/')
                $res = $res.'';
            else
                $res = $res.$newFilename[$i];

        }
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
                $res = '';
                $sn = $this->company->short_name;
                for ($i = 0; $i < strlen($sn); $i++)
                    if ($sn[$i] == ' ')
                        $res= $res.'_';
                    else
                        $res = $res.$sn[$i];
                $filename = 'Приложение'.$counter.'_Вх.'.$new_date.'_'.$this->local_number.'_'.$res.'_'.$this->document_theme;
            }
            else
            {
                $res = '';
                $sn = $this->company->name;
                for ($i = 0; $i < strlen($sn); $i++)
                    if ($sn[$i] == ' ')
                        $res= $res.'_';
                    else
                        $res = $res.$sn[$i];
                $filename = 'Приложение'.$counter.'_Вх.'.$new_date.'_'.$this->local_number.'_'.$res.'_'.$this->document_theme;
            }
            $newFilename = $filename;
            $res = '';
            for ($i = 0; $i < strlen($newFilename); $i++)
                if ($newFilename[$i] == ' ')
                    $res= $res.'_';
                else if ($newFilename[$i] == '"' || $newFilename[$i] == '/')
                    $res = $res.'';
                else
                    $res = $res.$newFilename[$i];

            $file->saveAs($path . $res . '.' . $file->extension);
            $result = $result.$res . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->applications = $result;
        else
            $this->applications = $this->applications.$result;
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
