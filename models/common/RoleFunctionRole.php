<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "role_function_role".
 *
 * @property int $id
 * @property int $role_function_id
 * @property int $role_id
 *
 * @property RoleFunction $roleFunction
 * @property Role $role
 */
class RoleFunctionRole extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'role_function_role';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['role_function_id', 'role_id'], 'required'],
            [['role_function_id', 'role_id'], 'integer'],
            [['role_function_id'], 'exist', 'skipOnError' => true, 'targetClass' => RoleFunction::className(), 'targetAttribute' => ['role_function_id' => 'id']],
            [['role_id'], 'exist', 'skipOnError' => true, 'targetClass' => Role::className(), 'targetAttribute' => ['role_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'role_function_id' => 'Role Function ID',
            'role_id' => 'Role ID',
        ];
    }

    /**
     * Gets query for [[RoleFunction]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoleFunction()
    {
        return $this->hasOne(RoleFunction::className(), ['id' => 'role_function_id']);
    }

    /**
     * Gets query for [[Role]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRole()
    {
        return $this->hasOne(Role::className(), ['id' => 'role_id']);
    }
}
