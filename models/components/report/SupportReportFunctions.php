<?php

namespace app\models\components\report;

use app\models\work\ForeignEventWork;
use app\models\work\TeacherParticipantBranchWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TeamWork;

class SupportReportFunctions
{
    //--Выгрузка id всех записей из массива--
    // Условие: наличие поля с именем 'id'
    static private function GetIdFromArray($array)
    {
        $IDs = [];
        foreach ($array as $item) $IDs[] = $item->id;
        return $IDs;
    }
    //---------------------------------------

    //--Поиск подходящих мероприятий--
    // Признак 1: окончание мероприятия попадает в промежуток [$start_date:$end_date]
    // Признак 2: подходящий уровень мероприятия
    static private function GetForeignEvents($start_date, $end_date, $event_level)
    {
        $events = ForeignEventWork::find()->where(['>=', 'finish_date', $start_date])
            ->andWhere(['<=', 'finish_date', $end_date])->andWhere(['IN', 'event_level_id', $event_level])->all();
        return $events;
    }
    //--------------------------------

    //--Функция для получения участников мероприятий по заданным параметрам--
    /*
     * $test_data - данные для тестирования функции (null - боевой режим, Object - тестовый режим)
     * [$start_date : $end_date] - Промежуток для поиска мероприятий. Мероприятие должно завершиться в заданный промежуток (границы включены)
     * $team_mode - учитывать команду как одного участника (1) или не учитывать команды (0)
     * $event_level - массив уровней мероприятия (региональный, федеральный...)
     * $branch - массив отделов (технопарк, кванториум...)
     * $focus - массив направленностей (теническая, соцпед...)
     * $allow_remote - форма реализации (очная, очная с дистантом...)
     * $unique - метод поиска участников (0 - все участники, 1 - уникальные участники)
     *
     * return [array(ForeignEventPartcipantId), array([team_id, team_id], [team_id, team_id]), кол-во участников с учетом/без учета команд]
     */

    /*
     * Данные для теста
     *
     * $events - мероприятия
     * $teacherParticipant - акты участия
     * $teacherParticipantBranches - связка "акты_участия -> отдел_учета"
     * $allTeamRows - все записи о командах-участниках
     * $result - массив ожидаемых результатов работы функции
     */
    static public function GetParticipants($test_data,
                                           $start_date, $end_date,
                                           $team_mode = 1,
                                           $event_level = ReportConst::EVENT_LEVELS,
                                           $branch = ReportConst::BRANCHES,
                                           $focus = ReportConst::FOCUSES,
                                           $allow_remote = ReportConst::ALLOW_REMOTES,
                                           $unique = 0)
    {
        // Получаем подходящие мероприятия
        $events = $test_data === null ? self::GetForeignEvents($start_date, $end_date, $event_level) : $test_data['events'];
        $eIds = self::GetIdFromArray($events);
        //--------------------------------

        // Получаем teacher_participant, подходящие под focus и allow_remote
        $teacherParticipants = $test_data === null ?
            TeacherParticipantWork::find()->where(['IN', 'foreign_event_id', $eIds])->andWhere(['IN', 'focus_id', $focus])->andWhere(['IN', 'allow_remote_id', $allow_remote])->all() :
            $test_data['teacherParticipant'];
        $tpIds = self::GetIdFromArray($teacherParticipants);
        //------------------------------------------------------------------

        // Получаем teacher_participant_branch, подходящие под branch
        $teacherParticipantBranches =  $test_data === null ?
            TeacherParticipantBranchWork::find()
            ->where(['IN', 'teacher_participant_id', $tpIds])->andWhere(['IN', 'branch_id', $branch])->all(\Yii::$app->db) :
            $test_data['teacherParticipantBranch'];
        //-----------------------------------------------------------

        // Получаем участников мероприятия с учетом unique
        $result = [];
        foreach ($teacherParticipantBranches as $one)
            $result[] = $one->teacherParticipantWork->participant_id;

        if ($unique)
            $result = array_unique($result);
        //------------------------------------------------

        // Получаем количество участников с учетом/без учета команд

        if ($team_mode == 1)
        {
            $teamArray = [];
            $countParticipants = count($result); //общее количество участников
            $allTeamRows = $test_data === null ?
                TeamWork::find()->where(['IN', 'teacher_participant_id', $result])->orderBy(['name' => SORT_ASC])->all() :
                $test_data['allTeamRows'];

            if ($allTeamRows !== null)
            {
                $currentTeamName = $allTeamRows[0]->name;

                foreach ($allTeamRows as $oneTeam)
                {
                    $tempTeamArr = []; //массив id для одной команды
                    if ($oneTeam->name === $currentTeamName) // проходим по одной команде (массив отсортирован по названию команды)
                    {
                        $tempTeamArr[] = $oneTeam->id;
                        $countParticipants--;
                    }
                    else // при смене команды - прибавляем прошлую команду к результату
                    {
                        $teamArray[] = $tempTeamArr;
                        $countParticipants++;
                        $currentTeamName = $oneTeam->name;
                    }

                }
            }

        }

        //---------------------------------------------------------

        asort($result);
        // если считаем с командами - то возвращаем данные по ним, иначе - null и стандартное количество участников в соответствии с unique
        return [$result, $team_mode ==  1 ? $teamArray : null, $team_mode == 1 ? $countParticipants : count($result)];
    }
    //-----------------------------------------------------------------------
}