<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "license_term_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property AsAdmin[] $asAdmins
 */
class LicenseTermType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'license_term_type';
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
        return $this->hasMany(AsAdmin::className(), ['license_term_type_id' => 'id']);
    }
}
