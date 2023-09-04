<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "access_level".
 *
 * @property int $id
 * @property int $user_id
 * @property int $role_function_id
 * @property string $start_time
 * @property string $end_time
 *
 * @property RoleFunction $roleFunction
 * @property User $user
 */
class AccessLevel extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'access_level';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'role_function_id', 'start_time', 'end_time'], 'required'],
            [['user_id', 'role_function_id'], 'integer'],
            [['start_time', 'end_time'], 'safe'],
            [['role_function_id'], 'exist', 'skipOnError' => true, 'targetClass' => RoleFunction::className(), 'targetAttribute' => ['role_function_id' => 'id']],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'role_function_id' => 'Role Function ID',
            'start_time' => 'Start Time',
            'end_time' => 'End Time',
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
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
