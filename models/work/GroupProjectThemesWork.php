<?php

namespace app\models\work;

use app\models\common\GroupProjectThemes;
use app\models\common\RoleFunction;
use app\models\common\User;
use app\models\common\ProjectTheme;
use app\models\common\TrainingGroup;
use app\models\work\ProjectThemeWork;
use Yii;


class GroupProjectThemesWork extends GroupProjectThemes
{
	public $themeName;
    public $themeDescription;

	public function rules()
    {
        return [
            [['training_group_id', 'project_theme_id'], 'required'],
            [['training_group_id', 'project_theme_id', 'project_type_id'], 'integer'],
            [['project_theme_id'], 'exist', 'skipOnError' => true, 'targetClass' => ProjectTheme::className(), 'targetAttribute' => ['project_theme_id' => 'id']],
            [['training_group_id'], 'exist', 'skipOnError' => true, 'targetClass' => TrainingGroup::className(), 'targetAttribute' => ['training_group_id' => 'id']],
            [['themeName', 'themeDescription'], 'string'],
        ];
    } 

    public function getProjectThemeWork()
    {
        return $this->hasOne(ProjectThemeWork::className(), ['id' => 'project_theme_id']);
    }   
}
