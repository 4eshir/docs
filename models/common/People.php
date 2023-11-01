<?php

namespace app\models\common;

use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "people".
 *
 * @property int $id
 * @property string $firstname
 * @property string $secondname
 * @property string $patronymic
 * @property string $short
 * @property int|null $company_id
 * @property int|null $position_id
 * @property int|null $branch_id
 * @property string $birthdate
 * @property int $sex
 * @property string $genitive
 *
 * @property Company $company
 * @property Position $position
 * @property Branch $branch
 */
class People extends \yii\db\ActiveRecord
{
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
            [['id', 'company_id', 'position_id', 'branch_id', 'sex'], 'integer'],
            [['id'], 'unique'],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['company_id' => 'id']],
            [['position_id'], 'exist', 'skipOnError' => true, 'targetClass' => Position::className(), 'targetAttribute' => ['position_id' => 'id']],
            [['branch_id'], 'exist', 'skipOnError' => true, 'targetClass' => Branch::className(), 'targetAttribute' => ['branch_id' => 'id']],
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
            'short' => 'Уникальный идентификатор',
            'company_id' => 'Company ID',
            'position_id' => 'Position ID',
            'branch_id' => 'Отдел по трудовому договору',
            'birthdate' => 'Дата рождения',
            'sex' => 'Пол',
            'genitive' => 'Фамилия в родительном падеже',
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
     * Gets query for [[TrainingProgramParticipants]].
     *
     * @return string
     */

    public function getBranch()
    {
        return $this->hasOne(Branch::className(), ['id' => 'branch_id']);
    }

}
