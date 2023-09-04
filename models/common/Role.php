<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "role".
 *
 * @property int $id
 * @property string $name
 *
 * @property RoleFunctionRole[] $roleFunctionRoles
 * @property UserRole[] $userRoles
 */
class Role extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'role';
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
     * Gets query for [[RoleFunctionRoles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoleFunctionRoles()
    {
        return $this->hasMany(RoleFunctionRole::className(), ['role_id' => 'id']);
    }

    /**
     * Gets query for [[UserRoles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUserRoles()
    {
        return $this->hasMany(UserRole::className(), ['role_id' => 'id']);
    }
}
