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
 * @property int $position_id
 * @property int $company_id
 *
 * @property DocumentIn[] $documentIns
 * @property DocumentOrder[] $documentOrders
 * @property DocumentOrder[] $documentOrders0
 * @property DocumentOrder[] $documentOrders1
 * @property DocumentOrder[] $documentOrders2
 * @property DocumentOut[] $documentOuts
 * @property DocumentOut[] $documentOuts0
 * @property Company $company
 * @property Position $position
 * @property Responsible[] $responsibles
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
            [['firstname', 'secondname', 'patronymic', 'position_id', 'company_id'], 'required'],
            [['position_id', 'comapny_id'], 'integer'],
            [['firstname', 'secondname', 'patronymic'], 'string', 'max' => 1000],
            [['company_id'], 'exist', 'skipOnError' => true, 'targetClass' => Company::className(), 'targetAttribute' => ['comapny_id' => 'id']],
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
            'position_id' => 'Position ID',
            'company_id' => 'Company ID',
        ];
    }

    /**
     * Gets query for [[DocumentIns]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentIns()
    {
        return $this->hasMany(DocumentIn::className(), ['signed_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentOrders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOrders()
    {
        return $this->hasMany(DocumentOrder::className(), ['bring_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentOrders0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOrders0()
    {
        return $this->hasMany(DocumentOrder::className(), ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentOrders1]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOrders1()
    {
        return $this->hasMany(DocumentOrder::className(), ['register_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentOrders2]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOrders2()
    {
        return $this->hasMany(DocumentOrder::className(), ['signed_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentOuts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOuts()
    {
        return $this->hasMany(DocumentOut::className(), ['executor_id' => 'id']);
    }

    /**
     * Gets query for [[DocumentOuts0]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDocumentOuts0()
    {
        return $this->hasMany(DocumentOut::className(), ['signed_id' => 'id']);
    }

    /**
     * Gets query for [[Company]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCompany()
    {
        return $this->hasOne(Company::className(), ['id' => 'comapny_id']);
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
     * Gets query for [[Responsibles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getResponsibles()
    {
        return $this->hasMany(Responsible::className(), ['people_id' => 'id']);
    }

    public function getFullName()
    {
        return $this->secondname.' '.$this->firstname.' '.$this->patronymic;
    }
}
