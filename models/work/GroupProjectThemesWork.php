<?php

namespace app\models\work;

use app\models\common\GroupProjectThemes;
use app\models\common\RoleFunction;
use app\models\common\User;
use Yii;


class GroupProjectThemesWork extends GroupProjectThemes
{
	public $themeName;

	public function rules()
    {
        return [
            ['themeName', 'string'],
        ];
    }    
}
