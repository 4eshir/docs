<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "local_responsibility".
 *
 * @property int $id
 * @property int $responsibility_type_id
 * @property int|null $branch_id
 * @property int|null $auditorium_id
 * @property int|null $quant
 * @property int|null $people_id
 * @property int|null $regulation_id
 * @property string|null $files
 *
 * @property Auditorium $auditorium
 * @property Branch $branch
 * @property People $people
 * @property Regulation $regulation
 * @property ResponsibilityType $responsibilityType
 */
class LocalResponsibility extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'local_responsibility';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['responsibility_type_id'], 'required'],
            [['files'], 'string', 'max' => 1000],
            [['quant'], 'integer'],
            [['auditorium_id'], 'exist', 'skipOnError' => true, 'targetClass' => Auditorium::className(), 'targetAttribute' => ['auditorium_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
            [['people_id'], 'exist', 'skipOnError' => true, 'targetClass' => People::className(), 'targetAttribute' => ['people_id' => 'id']],
            [['regulation_id'], 'exist', 'skipOnError' => true, 'targetClass' => Regulation::className(), 'targetAttribute' => ['regulation_id' => 'id']],
            [['responsibility_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => ResponsibilityType::className(), 'targetAttribute' => ['responsibility_type_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'responsibility_type_id' => 'Вид ответственности',
            'responsibilityTypeStr' => 'Вид ответственности',
            'branch_id' => 'Отдел',
            'branchStr' => 'Отдел',
            'auditorium_id' => 'Помещение',
            'auditoriumStr' => 'Помещение',
            'quant' => 'Квант',
            'people_id' => 'Работник',
            'peopleStr' => 'Работник',
            'regulation_id' => 'Положение/инструкция',
            'regulationStr' => 'Положение/инструкция',
            'files' => 'Файлы',
            'filesStr' => 'Файлы',
        ];
    }

    /**
     * Gets query for [[Auditorium]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAuditorium()
    {
        return $this->hasOne(Auditorium::className(), ['id' => 'auditorium_id']);
    }

    /**
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

    /**
     * Gets query for [[People]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getPeople()
    {
        return $this->hasOne(People::className(), ['id' => 'people_id']);
    }

    /**
     * Gets query for [[Regulation]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRegulation()
    {
        return $this->hasOne(Regulation::className(), ['id' => 'regulation_id']);
    }

    /**
     * Gets query for [[ResponsibilityType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsibilityType()
    {
        return $this->hasOne(ResponsibilityType::className(), ['id' => 'responsibility_type_id']);
    }
}
