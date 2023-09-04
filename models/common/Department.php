<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "department".
 *
 * @property int $id
 * @property string $name
 *
 * @property EventDepartment[] $eventDepartments
 */
class Department extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'department';
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
     * Gets query for [[EventDepartments]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getEventDepartments()
    {
        return $this->hasMany(EventDepartment::className(), ['department_id' => 'id']);
    }
}
