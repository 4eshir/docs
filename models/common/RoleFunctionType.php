<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "role_function_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property RoleFunction[] $roleFunctions
 */
class RoleFunctionType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'role_function_type';
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
     * Gets query for [[RoleFunctions]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getRoleFunctions()
    {
        return $this->hasMany(RoleFunction::className(), ['role_function_type_id' => 'id']);
    }
}
