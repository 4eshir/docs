<?php

namespace app\models\components\report;

use app\models\common\AllowRemote;
use app\models\test\work\GetParticipantsEventWork;
use app\models\test\work\GetParticipantsTeacherParticipantBranchWork;
use app\models\test\work\GetParticipantsTeacherParticipantWork;
use app\models\test\work\GetParticipantsTeamWork;
use app\models\work\AllowRemoteWork;
use app\models\work\BranchWork;
use app\models\work\EventLevelWork;
use app\models\work\FocusWork;
use app\models\work\ForeignEventWork;
use app\models\work\ParticipantAchievementWork;
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
        if ($array !== null)
            foreach ($array as $item) $IDs[] = $item->id;

        return $IDs;
    }
    //---------------------------------------

    //--Поиск подходящих мероприятий--
    // Признак 1: окончание мероприятия попадает в промежуток [$start_date:$end_date]
    // Признак 2: подходящий уровень мероприятия
    static private function GetForeignEvents($test_mode, $start_date, $end_date, $event_level)
    {
        $events = $test_mode == 0 ?
            ForeignEventWork::find()->where(['>=', 'finish_date', $start_date])
                ->andWhere(['<=', 'finish_date', $end_date])->andWhere(['IN', 'event_level_id', $event_level])->all() :
            GetParticipantsEventWork::find()->where(['>=', 'finish_date', $start_date])
                ->andWhere(['<=', 'finish_date', $end_date])->andWhere(['IN', 'event_level_id', $event_level])->all();
        return $events;
    }
    //--------------------------------

    //--Поиск подходящих актов участия teacher_participant--
    // Признак 1: в мероприятиях из массива eIds
    // Признак 2: заданных направленностей из массива focus
    // Признак 3: заданных форм реализации из массива allow_remote
    static private function GetTeacherParticipant($test_mode, $eIds, $tpbIds, $focus, $allow_remote)
    {
        $teacherParticipants = $test_mode == 0 ?
            TeacherParticipantWork::find()->where(['IN', 'foreign_event_id', $eIds])->andWhere(['IN', 'id', $tpbIds])->andWhere(['IN', 'focus', $focus])->andWhere(['IN', 'allow_remote_id', $allow_remote])->all() :
            GetParticipantsTeacherParticipantWork::find()->where(['IN', 'foreign_event_id', $eIds])->andWhere(['IN', 'id', $tpbIds])->andWhere(['IN', 'focus', $focus])->andWhere(['IN', 'allow_remote_id', $allow_remote])->all();
        return $teacherParticipants;
    }
    //-----------------------------------------------------

    //--Поиск подходящих teacher_participant_branch--
    // Признак 1: в актах участия из массива tpIds
    // Признак 2: заданных отделов из массива branch
    static private function GetTeacherParticipantBranch($test_mode, $branch)
    {
        $teacherParticipantsBranch = $test_mode == 0 ?
            TeacherParticipantBranchWork::find()->where(['IN', 'branch_id', $branch])->all() :
            GetParticipantsTeacherParticipantBranchWork::find()->where(['IN', 'branch_id', $branch])->all();
        return $teacherParticipantsBranch;
    }
    //-----------------------------------------------

    //-|---------------------------------------------------------------------|-
    //-| Функция для получения участников мероприятий по заданным параметрам |-
    //-|---------------------------------------------------------------------|-
    /*
     * $test_mode - режим запуска функции (0 - боевой, 1 - тестовый)
     * [$start_date : $end_date] - Промежуток для поиска мероприятий. Мероприятие должно завершиться в заданный промежуток (границы включены)
     * $team_mode - учитывать команду как одного участника (1) или не учитывать команды (0)
     * $unique - метод поиска участников (0 - все участники, 1 - уникальные участники)
     * $event_level - массив уровней мероприятия (региональный, федеральный...)
     * $branch - массив отделов (технопарк, кванториум...)
     * $focus - массив направленностей (теническая, соцпед...)
     * $allow_remote - форма реализации (очная, очная с дистантом...)
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
    static public function GetParticipants($test_mode,
                                           $start_date, $end_date,
                                           $team_mode = 1,
                                           $unique = 0,
                                           $event_level = EventLevelWork::ALL,
                                           $branch = BranchWork::ALL,
                                           $focus = FocusWork::ALL,
                                           $allow_remote = AllowRemoteWork::ALL)
    {
        // Получаем подходящие мероприятия
        $events = self::GetForeignEvents($test_mode, $start_date, $end_date, $event_level);
        $eIds = self::GetIdFromArray($events);
        //--------------------------------

        // Получаем teacher_participant_branch, подходящие под branch
        $teacherParticipantBranches = self::GetTeacherParticipantBranch($test_mode, $branch);
        $tpbIds = [];
        if ($teacherParticipantBranches !== null)
            foreach ($teacherParticipantBranches as $one) $tpbIds[] = $one->teacher_participant_id;
        //-----------------------------------------------------------

        // Получаем teacher_participant, подходящие под focus и allow_remote
        $teacherParticipants = self::GetTeacherParticipant($test_mode, $eIds, $tpbIds, $focus, $allow_remote);
        $tpIds = self::GetIdFromArray($teacherParticipants);
        //------------------------------------------------------------------



        // Получаем участников мероприятия с учетом unique
        $result = [];
        foreach ($teacherParticipants as $one)
            $result[] = $one->participant_id;

        if ($unique)
            $result = array_unique($result);
        //------------------------------------------------

        // Получаем количество участников с учетом/без учета команд

        $teamArray = [];
        $countParticipants = count($result); //общее количество участников


        /*
         * Примечание:
         * Если в команде присутствуют участники из разных отделов, а выборка охватывает только некоторых
         * из них - то учет участников ведется без учета тех, кто не относится к другим отделам
         *
         * Пример:
         * Команда Name
         * 3 участника - Технопарк
         * 2 участника - Кванториум
         * 2 участника - ЦОД
         *
         * При выборке участников из Технопарка и ЦОДа будет учтена 1 команда, состоящая из 5 участников
         */
        if ($team_mode == 1)
        {
            $allTeamRows = $test_mode === 0 ?
                TeamWork::find()->where(['IN', 'teacher_participant_id', $result])->orderBy(['name' => SORT_ASC, 'teacher_participant_id' => SORT_ASC])->all() :
                GetParticipantsTeamWork::find()->where(['IN', 'teacher_participant_id', $tpIds])->orderBy(['name' => SORT_ASC, 'teacher_participant_id' => SORT_ASC])->all();

            if ($allTeamRows !== null)
            {
                $currentTeamName = $allTeamRows[0]->name;

                $tempTeamArr = []; //массив id для одной команды
                foreach ($allTeamRows as $oneTeam)
                {
                    if ($oneTeam->name === $currentTeamName) // проходим по одной команде (массив отсортирован по названию команды)
                    {
                        $tempTeamArr[] = $oneTeam->id;
                    }
                    else // при смене команды - прибавляем прошлую команду к результату
                    {
                        $teamArray[] = $tempTeamArr;
                        $tempTeamArr = array();
                        $currentTeamName = $oneTeam->name;
                        $tempTeamArr[] = $oneTeam->id;
                    }
                    $countParticipants--; // за каждого участника команды вычитаем единицу из общего числа участников
                }

                $teamArray[] = $tempTeamArr;
                $countParticipants += count($teamArray); // увеличиваем общее количество участников на количество команд
            }

        }

        //-----------------------------------------------------------

        sort($result);
        // если считаем с командами - то возвращаем данные по ним, иначе - null и стандартное количество участников в соответствии с unique
        return [$result, $teamArray, $countParticipants];
    }
    //-----------------------------------------------------------------------




    //-|---------------------------------------------------------------------|-
    //-| Функция для получения победителей и призеров по заданным параметрам |-
    //-|---------------------------------------------------------------------|-
    /*
     * $participants - список участников, из которого будет произведена выборка
     * $achieve_mode - массив типов победителей (победители, призеры)
     * $unique_achieve - считать победителя/призера один раз или каждый акт
     *
     * return [array(ForeignEventPartcipantId), array([team_id, team_id], [team_id, team_id]), кол-во участников с учетом/без учета команд]
     */
    static public function GetParticipantAchievements($participants,
                                                      $achieve_mode = ParticipantAchievementWork::ALL,
                                                      $unique_achieve = 0)
    {

    }
    //-------------------------------------------------------------------------
}