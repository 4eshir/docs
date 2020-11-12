<?php

namespace app\models\common;

use Faker\Provider\File;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "document_out".
 *
 * @property int $id
 * @property int $document_number
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
    public $applicationFiles;
    public $signedString;
    public $executorString;
    public $registerString;
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
            [['scanFile'], 'file', 'extensions' => 'png, jpg, pdf', 'skipOnEmpty' => true],
            [['applicationFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'png, jpg, pdf, doc, docx', 'maxFiles' => 10,'checkExtensionByMimeType'=>false],

            [['signedString', 'executorString', 'registerString', 'key_words'], 'string', 'message' => 'Введите корректные ФИО'],
            [['document_number', 'document_name', 'document_date', 'document_theme', 'signed_id', 'executor_id', 'send_method_id', 'sent_date', 'register_id', 'document_number', 'signedString', 'executorString'], 'required', 'message' => 'Данное поле не может быть пустым'],
            [['document_date', 'sent_date'], 'safe'],
            [['company_id', 'position_id', 'signed_id', 'executor_id', 'send_method_id', 'register_id'], 'integer'],
            [['document_theme', 'Scan', 'key_words'], 'string', 'max' => 1000],
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
            'document_number' => 'Document Number',
            'document_date' => 'Document Date',
            'document_theme' => 'Document Theme',
            'company_id' => 'Company ID',
            'position_id' => 'Position ID',
            'signed_id' => 'Signed ID',
            'executor_id' => 'Executor ID',
            'send_method_id' => 'Send Method ID',
            'sent_date' => 'Sent Date',
            'Scan' => 'Scan',
            'applications' => 'Applications',
            'register_id' => 'Register ID',
            'key_words' => 'Key Words',
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

    public function uploadScanFile()
    {
        $path = '@app/upload/files/document_out/scan/';
        $date = $this->document_date;
        $new_date = '';
        for ($i = 0; $i < strlen($date); ++$i)
            if ($date[$i] != '-')
                $new_date = $new_date.$date[$i];
        $filename = 'Исх.'.$new_date.'_'.$this->document_number.'_'.$this->company->short_name.'_'.$this->document_theme;
        $this->Scan = $filename . '.' . $this->scanFile->extension;
        $this->scanFile->saveAs( $path . $filename . '.' . $this->scanFile->extension);
    }

    public function uploadApplicationFiles($upd = null)
    {
        $result = '';
        foreach ($this->applicationFiles as $file) {

            do{
                $filename = Yii::$app->getSecurity()->generateRandomString(15);
            }while(file_exists('@app/upload/files/' . $filename . '.' . $file->extension));

            $file->saveAs('@app/upload/files/' . $filename . '.' . $file->extension);
            $result = $result.$filename . '.' . $file->extension.' ';
        }
        if ($upd == null)
            $this->applications = $result;
        else
            $this->applications = $this->applications.$result;
        return true;
    }

    public function getDocumentNumber()
    {
        $max = DocumentOut::find()->max('document_number');
        if ($max == null)
            $max = 1;
        else
            $max = $max + 1;
        return $max;
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
