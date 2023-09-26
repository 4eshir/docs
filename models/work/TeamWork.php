<?php

namespace app\models\work;

use app\models\common\Team;
use app\models\null\TeacherParticipantNull;
use app\models\null\TeamNameNull;
use app\models\work\TeamNameWork;
use Yii;


class TeamWork extends Team
{
    const TEAM_ON = 1;
    const TEAM_OFF = 0;

    public function getTeamNameWork()
    {
        $try = $this->hasOne(TeamNameWork::className(), ['id' => 'team_name_id']);
        return $try->all() ? $try : new TeamNameNull();
    }

    public function checkCollectionTeamName()
    {
        // возвращает информацию о наличии связанных детей и команд (если обнаружится команда без связки с ребенком, то её нужно удалить)

        $teamPart = TeamWork::find()->where(['team_name_id' => $this->team_name_id])->all();

        if ($teamPart == null)
            return true;
        else
            return false;
    }

    public function getTeacherParticipantWork()
    {
        $try = $this->hasOne(TeacherParticipantWork::className(), ['id' => 'teacher_participant_id']);
        return $try->all() ? $try : new TeacherParticipantNull();
    }
}
