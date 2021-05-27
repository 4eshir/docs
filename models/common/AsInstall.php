<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "as_install".
 *
 * @property int $id
 * @property int $install_place_id
 * @property int $as_admin_id
 * @property string $cabinet
 * @property int $count
 *
 * @property AsAdmin $asAdmin
 * @property InstallPlace $installPlace
 */
class AsInstall extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'as_install';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['install_place_id', 'as_admin_id', 'count'], 'integer'],
            [['cabinet'], 'string', 'max' => 1000],
            [['as_admin_id'], 'exist', 'skipOnError' => true, 'targetClass' => AsAdmin::className(), 'targetAttribute' => ['as_admin_id' => 'id']],
            [['install_place_id'], 'exist', 'skipOnError' => true, 'targetClass' => InstallPlace::className(), 'targetAttribute' => ['install_place_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'install_place_id' => 'Branch ID',
            'as_admin_id' => 'As Admin ID',
            'cabinet' => 'Cabinet',
            'count' => 'Count',
        ];
    }

    /**
     * Gets query for [[AsAdmin]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getAsAdmin()
    {
        return $this->hasOne(AsAdmin::className(), ['id' => 'as_admin_id']);
    }

    /**
     * Gets query for [[Branch]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getInstallPlace()
    {
        return $this->hasOne(InstallPlace::className(), ['id' => 'install_place_id']);
    }
}
