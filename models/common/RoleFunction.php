<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "role_function".
 *
 * @property int $id
 * @property string $name
 * @property int $role_function_type_id
 *
 * @property RoleFunctionType $roleFunctionType
 * @property RoleFunctionRole[] $roleFunctionRoles
 */
class RoleFunction extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'role_function';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'role_function_type_id'], 'required'],
            [['role_function_type_id'], 'integer'],
            [['name'], 'string', 'max' => 1000],
            [['role_function_type_id'], 'exist', 'skipOnError' => true, 'targetClass' => RoleFunctionType::className(), 'targetAttribute' => ['role_function_type_id' => 'id']],
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
            'role_function_type_id' => 'Role Function Type ID',
        ];
    }

    /**
     * Gets query for [[RoleFunctionType]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoleFunctionType()
    {
        return $this->hasOne(RoleFunctionType::className(), ['id' => 'role_function_type_id']);
    }

    /**
     * Gets query for [[RoleFunctionRoles]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoleFunctionRoles()
    {
        return $this->hasMany(RoleFunctionRole::className(), ['role_function_id' => 'id']);
    }
}
