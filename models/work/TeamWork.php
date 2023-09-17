<?php

namespace app\models\work;

use app\models\common\Team;
use app\models\work\TeamNameWork;
use Yii;


class TeamWork extends Team
{
    public function getTeamNameWork()
    {
        return $this->hasOne(TeamNameWork::className(), ['id' => 'team_name_id']);
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
        return $this->hasOne(TeacherParticipantWork::className(), ['id' => 'teacher_participant_id']);
    }
}
