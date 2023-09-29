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
 * @property int $creator_id
 * @property int $last_edit
 * @property string $key_words
 *
 * @property People $executor
 * @property People $creator
 * @property People $lastEdit
 * @property People $correspondent
 * @property SendMethod $sendMethod
 * @property People $signed
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
            [['document_name', 'document_date', 'document_theme', 'signed_id', 'executor_id', 'send_method_id', 'sent_date', 'creator_id', 'document_number', 'signedString', 'executorString'], 'required'],
            [['company_id', 'position_id', 'signed_id', 'executor_id', 'send_method_id', 'creator_id', 'last_edit_id', 'document_postfix', 'document_number'], 'integer'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::className(), 'targetAttribute' => ['position_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['executor_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['creator_id' => 'id']],
            [['last_edit_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['last_edit_id' => 'id']],
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
            'creator_id' => 'Кто зарегистрировал',
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
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
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
}
