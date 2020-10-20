<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "document_out".
 *
 * @property int $id
 * @property string $document_date
 * @property string $document_theme
 * @property int $destination_id
 * @property int $signed_id
 * @property int $executor_id
 * @property int $send_method_id
 * @property string $sent_date
 * @property string $Scan
 * @property int $register_id
 *
 * @property Destination $destination
 * @property People $executor
 * @property People $register
 * @property SendMethod $sendMethod
 * @property People $signed
 * @property File[] $files
 */
class DocumentOut extends \yii\db\ActiveRecord
{
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
            [['document_date', 'document_theme', 'destination_id', 'signed_id', 'executor_id', 'send_method_id', 'sent_date', 'Scan', 'register_id'], 'required'],
            [['document_date', 'sent_date'], 'safe'],
            [['destination_id', 'signed_id', 'executor_id', 'send_method_id', 'register_id'], 'integer'],
            [['document_theme', 'Scan'], 'string', 'max' => 1000],
            [['destination_id'], 'exist', 'skipOnError' => true, 'targetClass' => Destination::className(), 'targetAttribute' => ['destination_id' => 'id']],
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
            'document_date' => 'Document Date',
            'document_theme' => 'Document Theme',
            'destination_id' => 'Destination ID',
            'signed_id' => 'Signed ID',
            'executor_id' => 'Executor ID',
            'send_method_id' => 'Send Method ID',
            'sent_date' => 'Sent Date',
            'Scan' => 'Scan',
            'register_id' => 'Register ID',
        ];
    }

    /**
     * Gets query for [[Destination]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDestination()
    {
        return $this->hasOne(Destination::className(), ['id' => 'destination_id']);
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
}
