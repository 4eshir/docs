<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "as_company".
 *
 * @property int $id
 * @property string $name
 *
 * @property AsAdmin[] $asAdmins
 */
class AsCompany extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'as_company';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 1000],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[AsAdmins]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsAdmins()
    {
        return $this->hasMany(AsAdmin::className(), ['as_company_id' => 'id']);
    }
}
