<?php

namespace app\models\common;

use app\models\components\FileWizard;
use Yii;

/**
 * This is the model class for table "document_order".
 *
 * @property int $id
 * @property int $order_copy_id
 * @property string $order_number
 * @property string $order_postfix
 * @property string $order_name
 * @property string $order_date
 * @property int $signed_id
 * @property int $bring_id
 * @property int $executor_id
 * @property int $scan
 * @property int $doc
 * @property int $register_id
 * @property int $type
 * @property string $key_words
 * @property boolean $state
 *
 * @property People $bring
 * @property People $executor
 * @property People $register
 * @property People $signed
 * @property Responsible[] $responsibles
 */
class DocumentOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'document_order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [

            [['signed_id', 'bring_id', 'executor_id', 'register_id', 'order_postfix', 'order_copy_id', 'type'], 'integer'],
            [['state'], 'boolean'],
            [['order_name', 'scan', 'key_words'], 'string', 'max' => 1000],
            [['order_number'], 'string', 'max' => 100],
            [['bring_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['bring_id' => 'id']],
            [['executor_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['executor_id' => 'id']],
            [['register_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['register_id' => 'id']],
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
            'order_number' => 'Номер приказа',
            'documentNumberString' => 'Номер приказа',
            'order_name' => 'Наименование приказа',
            'order_date' => 'Дата приказа',
            'signed_id' => 'Кем подписан',
            'bring_id' => 'Проект вносит',
            'executor_id' => 'Кто исполнил',
            'scan' => 'Скан',
            'register_id' => 'Кто регистрировал',
            'type' => 'По основной деятельности',
        ];
    }

    /**
     * Gets query for [[Bring]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBring()
    {
        return $this->hasOne(People::className(), ['id' => 'bring_id']);
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
     * Gets query for [[Signed]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getSigned()
    {
        return $this->hasOne(People::className(), ['id' => 'signed_id']);
    }

    /**
     * Gets query for [[Responsibles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsibles()
    {
        return $this->hasMany(Responsible::className(), ['document_order_id' => 'id']);
    }
}