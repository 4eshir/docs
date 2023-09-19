<?php

namespace app\models\null;

use app\models\work\GroupProjectThemesWork;

class GroupProjectThemesNull extends GroupProjectThemesWork
{
    function __construct()
    {
        $this->training_group_id = null;
        $this->project_theme_id = null;
        $this->project_type_id = null;
        $this->confirm = null;
        $this->themeName = null;
        $this->themeDescription = null;
    }

}