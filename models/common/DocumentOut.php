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
 * @property int $company_id
 * @property int $position_id
 * @property int $signed_id
 * @property int $executor_id
 * @property int $send_method_id
 * @property string $sent_date
 * @property string $Scan
 * @property int $register_id
 *
 * @property People $executor
 * @property People $register
 * @property SendMethod $sendMethod
 * @property People $signed
 * @property File[] $files
 */
class DocumentOut extends \yii\db\ActiveRecord
{
    public $scanFile;
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
            [['scanFile'], 'file', 'extensions' => 'txt, png', 'skipOnEmpty' => true],

            [['document_name', 'document_date', 'document_theme', 'signed_id', 'executor_id', 'send_method_id', 'sent_date', 'register_id', 'document_number'], 'required'],
            [['document_date', 'sent_date'], 'safe'],
            [['company_id', 'position_id', 'signed_id', 'executor_id', 'send_method_id', 'register_id', 'document_number'], 'integer'],
            [['document_theme', 'Scan'], 'string', 'max' => 1000],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::className(), 'targetAttribute' => ['position_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['executor_id' => 'id']],
            [['register_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['register_id' => 'id']],
            [['send_method_id'], 'exist', 'skipOnError' => true, 'targetClass' => SendMethod::className(), 'targetAttribute' => ['send_method_id' => 'id']],
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
            'register_id' => 'Register ID',
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
        return $this->hasOne(People::className(), ['id' => 'register_id']);
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
     * Gets query for [[Files]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getFiles()
    {
        return $this->hasMany(File::className(), ['document_id' => 'id']);
    }

    public function getImagesLinks()
    {
        $path = ArrayHelper::getColumn(self::find()->all(), Yii::$app->basePath.'/upload/files/'.$this->Scan);
        return $path;
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
