<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "project_type".
 *
 * @property int $id
 * @property string $name
 *
 * @property GroupProjectThemes[] $groupProjectThemes
 */
class ProjectType extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_type';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 100],
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
     * Gets query for [[GroupProjectThemes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroupProjectThemes()
    {
        return $this->hasMany(GroupProjectThemes::className(), ['project_type_id' => 'id']);
    }
}
