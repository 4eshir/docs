<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "install_place".
 *
 * @property int $id
 * @property string $name
 *
 * @property AsInstall[] $asInstalls
 */
class InstallPlace extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'install_place';
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
     * Gets query for [[AsInstalls]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsInstalls()
    {
        return $this->hasMany(AsInstall::className(), ['install_place_id' => 'id']);
    }
}
