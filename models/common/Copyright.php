<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "copyright".
 *
 * @property int $id
 * @property string $name
 *
 * @property AsAdmin[] $asAdmins
 */
class Copyright extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'copyright';
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
        return $this->hasMany(AsAdmin::className(), ['copyright_id' => 'id']);
    }
}
