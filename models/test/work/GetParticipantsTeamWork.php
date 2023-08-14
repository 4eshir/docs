<?php

namespace app\models\test\work;

use app\models\test\common\GetParticipantsTeam;

class GetParticipantsTeamWork extends GetParticipantsTeam
{
    public function __construct($t_name = null, $t_teacher_participant_id = null)
    {
        if ($t_name === null)
            parent::__construct();
        else
        {
            $this->name = $t_name;
            $this->teacher_participant_id = $t_teacher_participant_id;
        }
    }

    //--Функция сравнения двух массивов команд в формате:
    // [array, array...]
    // array - [participant_id, participant_id...]
    static public function CheckTeamEqual($teams1, $teams2)
    {
        if (count($teams1) !== count($teams2)) return false;

        for ($i = 0; $i < count($teams1); $i++)
        {
            if (count($teams1[$i]) !== count($teams2[$i])) return false;
            for ($j = 0; $j < count($teams1[$i]); $j++)
                if ($teams1[$i][$j] !== $teams2[$i][$j])
                    return false;
        }

        return true;
    }
}