<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "people".
 *
 * @property int $id
 * @property string $firstname
 * @property string $secondname
 * @property string $patronymic
 * @property int|null $company_id
 * @property int|null $position_id
 *
 * @property Company $company
 * @property Position $position
 */
class People extends \yii\db\ActiveRecord
{
    public $stringPosition;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'people';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'firstname', 'secondname', 'patronymic'], 'required'],
            [['id', 'company_id', 'position_id'], 'integer'],
            [['firstname', 'secondname', 'patronymic', 'stringPosition'], 'string', 'max' => 1000],
            [['id'], 'unique'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::className(), 'targetAttribute' => ['position_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'firstname' => 'Firstname',
            'secondname' => 'Secondname',
            'patronymic' => 'Patronymic',
            'company_id' => 'Company ID',
            'position_id' => 'Position ID',
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

    public function checkForeignKeys()
    {
        $doc_out_signed = DocumentOut::find()->where(['signed_id' => $this->id])->all();
        $doc_out_exec = DocumentOut::find()->where(['executor_id' => $this->id])->all();
        $doc_in_corr = DocumentIn::find()->where(['correspondent_id' => $this->id])->all();
        $doc_in_signed = DocumentIn::find()->where(['signed_id' => $this->id])->all();
        if (count($doc_out_signed) > 0 || count($doc_out_exec) > 0 || count($doc_in_corr) > 0 || count($doc_in_signed) > 0)
        {

            Yii::$app->session->addFlash('error', 'Невозможно удалить человека! Человек включен в существующие документы');
            return false;
        }
        return true;
    }

    public function getFullName()
    {
        return $this->secondname.' '.$this->firstname.' '.$this->patronymic;
    }

    public function beforeSave($insert)
    {
        if ($this->stringPosition == '')
            $this->stringPosition = '---';
        $position = Position::find()->where(['name' => $this->stringPosition])->one();
        if ($position !== null)
            $this->position_id = $position->id;
        else
        {
            $position = new Position();
            $position->name = $this->stringPosition;
            $position->save();
            $newPos = Position::find()->where(['name' => $this->stringPosition])->one();
            $this->position_id = $newPos->id;
        }
        return parent::beforeSave($insert);
    }
}
