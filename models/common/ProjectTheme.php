<?php

namespace app\models\common;

use Yii;

/**
 * This is the model class for table "project_theme".
 *
 * @property int $id
 * @property string $name
 * @property string $description
 *
 * @property GroupProjectThemes[] $groupProjectThemes
 */
class ProjectTheme extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project_theme';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 1000],
            [['description'], 'string', 'max' => 2000],
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
            'description' => 'Description',
        ];
    }

    /**
     * Gets query for [[GroupProjectThemes]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getGroupProjectThemes()
    {
        return $this->hasMany(GroupProjectThemes::className(), ['project_theme_id' => 'id']);
    }
}
