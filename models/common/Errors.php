<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "errors".
 *
 * @property int $id
 * @property string $number
 * @property string $name
 *
 * @property GroupErrors[] $groupErrors
 */
class Errors extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'errors';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'number', 'name'], 'required'],
            [['id'], 'integer'],
            [['number'], 'string', 'max' => 5],
            [['name'], 'string', 'max' => 1000],
            [['id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Number',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[GroupErrors]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroupErrors()
    {
        return $this->hasMany(GroupErrors::className(), ['errors_id' => 'id']);
    }
}
