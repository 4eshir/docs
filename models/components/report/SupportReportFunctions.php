<?php

namespace app\models\components\report;

use app\models\common\AllowRemote;
use app\models\common\TeamName;
use app\models\test\work\GetParticipantAchievementsParticipantAchievementWork;
use app\models\test\work\GetParticipantsEventWork;
use app\models\test\work\GetParticipantsTeacherParticipantBranchWork;
use app\models\test\work\GetParticipantsTeacherParticipantWork;
use app\models\test\work\GetParticipantsTeamNameWork;
use app\models\test\work\GetParticipantsTeamWork;
use app\models\work\AllowRemoteWork;
use app\models\work\BranchWork;
use app\models\work\EventLevelWork;
use app\models\work\FocusWork;
use app\models\work\ForeignEventWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TeacherParticipantBranchWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TeamNameWork;
use app\models\work\TeamWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use yii\db\Query;

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
    static private function GetTeacherParticipant($test_mode, $eIds, $tpbIds, $focus, $allow_remote, $team_participants_id)
    {
        $teacherParticipants = $test_mode == 0 ?
            TeacherParticipantWork::find()->where(['IN', 'foreign_event_id', $eIds])->andWhere(['IN', 'id', $tpbIds])->andWhere(['IN', 'focus', $focus])->andWhere(['IN', 'allow_remote_id', $allow_remote])->andWhere(['NOT IN', 'id', $team_participants_id])->all() :
            GetParticipantsTeacherParticipantWork::find()->where(['IN', 'foreign_event_id', $eIds])->andWhere(['IN', 'id', $tpbIds])->andWhere(['IN', 'focus', $focus])->andWhere(['IN', 'allow_remote_id', $allow_remote])->andWhere(['NOT IN', 'id', $team_participants_id])->
                orderBy(['participant_id' => SORT_ASC])->all();
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

    //--Выгрузка teacher_participant с уникальными participant_id--
    static private function GetUniqueTeacherParticipantId($query)
    {
        $result = [];

        $currentParticipantId = $query[0]->participant_id;
        $result[] = $query[0]->id;

        foreach ($query as $one)
            if ($one->participant_id !== $currentParticipantId)
            {
                $result[] = $one->id;
                $currentParticipantId = $one->participant_id;
            }

        sort($result);
        return $result;
    }
    //-------------------------------------------------------------

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
     * return [array(ForeignEventPartcipantId), array([team_id, team_id], [team_id, team_id]), кол-во участников с учетом/без учета команд, список id записей таблицы teacher_participant]
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

        $teamParticipantsId = []; //участники команд

        //--Получаем команды, для удаления участников команд из teacher_participant--

        //--Получаем все команды с заданных мероприятий--
        $allTeamRows = $test_mode == 0 ?
            TeamNameWork::find()->where(['IN', 'foreign_event_id', $eIds])->all() :
            GetParticipantsTeamNameWork::find()->where(['IN', 'foreign_event_id', $eIds])->all();
        //-----------------------------------------------

        $teamArray = self::GetIdFromArray($allTeamRows);

        //--Получаем участников команд--
        $teamParticipants = $test_mode == 0 ?
            TeamWork::find()->where(['IN', 'team_name_id', $teamArray])->all() :
            GetParticipantsTeamWork::find()->where(['IN', 'team_name_id', $teamArray])->all();


        foreach ($teamParticipants as $one) $teamParticipantsId[] = $one->teacher_participant_id;
        //------------------------------

        //---------------------------------------------------------------------------

        // Получаем teacher_participant_branch, подходящие под branch
        $teacherParticipantBranches = self::GetTeacherParticipantBranch($test_mode, $branch);
        $tpbIds = [];
        if ($teacherParticipantBranches !== null)
            foreach ($teacherParticipantBranches as $one) $tpbIds[] = $one->teacher_participant_id;
        //-----------------------------------------------------------

        // Получаем teacher_participant, подходящие под focus и allow_remote и не входящие в состав команд
        $teacherParticipants = self::GetTeacherParticipant($test_mode, $eIds, $tpbIds, $focus, $allow_remote, $teamParticipantsId);
        $tpIds = $unique == 0 ?
            self::GetIdFromArray($teacherParticipants) :
            self::GetUniqueTeacherParticipantId($teacherParticipants);
        //------------------------------------------------------------------------------------------------



        // Получаем участников мероприятия с учетом unique
        $result = [];
        foreach ($teacherParticipants as $one)
            $result[] = $one->participant_id;

        if ($unique)
            $result = array_unique($result);
        //------------------------------------------------

        // Получаем количество участников с учетом/без учета команд

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
            //--Получаем всех участников из teacher_aprticipant--
            $teacherParticipantsAll = self::GetTeacherParticipant($test_mode, $eIds, $tpbIds, $focus, $allow_remote, []);
            $tpmIds = self::GetIdFromArray($teacherParticipantsAll);
            //--Получаем всех участников команд, соответствующих заданным условиям (относительно самих участников)--
            $teamParticipants = $test_mode == 0 ?
                TeamWork::find()->where(['IN', 'team_name_id', $teamArray])->andWhere(['IN', 'teacher_participant_id', $tpmIds])->all() :
                GetParticipantsTeamWork::find()->where(['IN', 'team_name_id', $teamArray])->andWhere(['IN', 'teacher_participant_id', $tpmIds])->all();
            //------------------------------------------------------------------------------------------------------

            //--Находим команды, в которых есть участники, подходящие под заданные все условия--
            $realTeamsId = [];
            foreach ($teamParticipants as $one) $realTeamsId[] = $one->team_name_id;

            $realTeams = $test_mode == 0 ?
                TeamNameWork::find()->where(['IN', 'id', $realTeamsId])->all() :
                GetParticipantsTeamNameWork::find()->where(['IN', 'id', $realTeamsId])->all();

            $teamArray = self::GetIdFromArray($realTeams);
            //----------------------------------------------------------------------------------


            //$countParticipants -= count($teamParticipants);
            $countParticipants += count($realTeams);
        }

        //-----------------------------------------------------------

        sort($result);
        sort($teamArray);
        // если считаем с командами - то возвращаем данные по ним, иначе - null и стандартное количество участников в соответствии с unique
        return [$result, $team_mode == 0 ? [] : $teamArray, $countParticipants, $tpIds, $tpmIds, $teamArray];
    }
    //-----------------------------------------------------------------------


    //--Выгрузка уникальных participant_id из массива teacher_participant--
    static private function GetUniqueParticipantAchievementId($query)
    {
        $result = [];

        $currentParticipantId = $query[0]->teacherParticipant->participant_id;
        $result[] = $query[0]->id;

        foreach ($query as $one)
            if ($one->teacherParticipant->participant_id !== $currentParticipantId)
            {
                $result[] = $one->id;
                $currentParticipantId = $one->teacherParticipant->participant_id;
            }

        return $result;
    }
    //---------------------------------------------------------------------


    //-|---------------------------------------------------------------------|-
    //-| Функция для получения победителей и призеров по заданным параметрам |-
    //-|---------------------------------------------------------------------|-
    /*
     * $test_mode - режим запуска функции (0 - боевой, 1 - тестовый)
     * $participants - список актов участия, из которого будет произведена выборка (teacher_participant)
     * $unique_achieve - считать победителя/призера один раз или каждый акт (0 - считать всех, 1 - считать один раз)
     * $achieve_mode - массив типов победителей (победители, призеры)
     *
     * return [array(ParticipantAchievement)]
     */
    static public function GetParticipantAchievements($test_mode,
                                                      $participants,
                                                      $unique_achieve = 0,
                                                      $achieve_mode = ParticipantAchievementWork::ALL)
    {
        $achievements = $test_mode == 0 ?
            ParticipantAchievementWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['IN', 'teacher_participant_id', $participants[3]])->andWhere(['IN', 'winner', $achieve_mode]) :
            GetParticipantAchievementsParticipantAchievementWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['IN', 'teacher_participant_id', $participants[3]])->andWhere(['IN', 'winner', $achieve_mode]);

        $achievements = $unique_achieve == 0 ?
            self::GetIdFromArray($achievements->orderBy(['teacherParticipant.participant_id' => SORT_ASC])->all()) :
            self::GetUniqueParticipantAchievementId($achievements->orderBy(['teacherParticipant.participant_id' => SORT_ASC])->all());

        $achievementsTeam = self::GetIdFromArray($test_mode == 0 ?
            ParticipantAchievementWork::find()->where(['IN', 'team_name_id', $participants[1]])->andWhere(['IN', 'winner', $achieve_mode])->all() :
            GetParticipantAchievementsParticipantAchievementWork::find()->where(['IN', 'team_name_id', $participants[1]])->andWhere(['IN', 'winner', $achieve_mode])->all());

        $achievements = array_merge($achievements, $achievementsTeam);

        sort($achievements);
        return $achievements;
    }
    //-------------------------------------------------------------------------


    //--Функция проверки возраста обучающегося--
    static public function CheckAge($birthday, $ages)
    {
        $birthday_timestamp = strtotime($birthday);
        $age = date('Y') - date('Y', $birthday_timestamp);
        if (date('md', $birthday_timestamp) > date('md')) $age--;

        return in_array($age, $ages);
    }
    //------------------------------------------


    //--Функция выгрузки всех учебных групп, подходящих под заданные условия--
    static public function GetTrainingGroups($start_date, $end_date, $branch, $focus, $allow_remote, $budget, $teachers)
    {
        $teacherGroups = TeacherGroupWork::find()->joinWith(['trainingGroup trainingGroup'])->joinWith(['trainingGroup.trainingProgram trainingProgram'])
            ->where(['IN', 'training_group_id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])])
            ->orWhere(['IN', 'training_group_id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])])
            ->orWhere(['IN', 'training_group_id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])])
            ->orWhere(['IN', 'training_group_id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])])
            ->andWhere(['IN', 'trainingGroup.branch', $branch])
            ->andWhere(['IN', 'trainingGroup.budget', $budget])
            ->andWhere(['IN', 'trainingProgram.focus_id', $focus])
            ->andWhere(['IN', 'trainingProgram.allow_remote_id', $allow_remote])
            ->andWhere($teachers == [] ? [1] : ['IN', 'teacher_id', $teachers])
            ->all();

        $tgId = [];
        foreach ($teacherGroups as $one) $tgId[] = $one->training_group_id;

        return TrainingGroupWork::find()->where(['IN', 'id', $tgId])->all();
    }
    //------------------------------------------------------------------------


    //--Функция выгрузки обучающихся, соответствующих заданным параметрам, из учебных групп--
    static public function GetParticipantsFromGroup($groups, $unique, $age)
    {
        $groupIds = self::GetIdFromArray($groups);

        //--Находим подходящих по группе обучающихся--
        $participants = TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groupIds])->orderBy(['participant_id' => SORT_ASC])->all();
        //--------------------------------------------

        //--Производим отбор по возрасту и удаляем дубликаты (при необходимости)--
        $currentParticipant = $participants[0]->participant_id; // текущий id обучающегося (для уникального режима)
        $resultParticipant = $unique == 0 ? [] : [$participants[0]]; // если считаем уникальных - то первого сразу заносим в список
        foreach ($participants as $participant)
        {
            if ($age !== ReportConst::AGES_ALL)
                if (!self::CheckAge($participant->participant->birthdate, $age))
                    continue;

            if ($unique == 1)
            {
                if ($participant->participant_id == $currentParticipant)
                    continue;
                else
                {
                    // Обновление уникального обучающегося
                    $currentParticipant = $participant->participant_id;
                    $resultParticipant[] = $participant;
                    //------------------------------------
                }
            }
            else
                $resultParticipant[] = $participant;
        }
        //------------------------------------------------------------------------
        return $resultParticipant;
    }
    //---------------------------------------------------------------------------------------


    //-|---------------------------------------------------------------------------|-
    //-| Функция для получения обучающихся из учебных групп по заданным параметрам |-
    //-|---------------------------------------------------------------------------|-
    /*
     * $test_mode - режим запуска функции (0 - боевой, 1 - тестовый)
     * $start_date - левая граница дат
     * $end_date - правая граница дат
     * $branch - массив отделов
     * $focus - массив направленностей
     * $allow_remote - массив форм реализации
     * $budget - массив типов групп по признакам бюджет/внебюджет
     * $teachers - массив педагогов групп. Если массив пустой - то учитываются все педагоги
     * $unique - тип выгрузки обучающихся (0 - все, 1 - уникальные)
     * $age - массив возрастов обучающихся
     */
    static public function GetGroupParticipants($test_mode,
                                                $start_date, $end_date,
                                                $branch = BranchWork::ALL,
                                                $focus = FocusWork::ALL,
                                                $allow_remote = AllowRemoteWork::ALL,
                                                $budget = ReportConst::BUDGET_ALL,
                                                $teachers = [],
                                                $unique = 0,
                                                $age = ReportConst::AGES_ALL)
    {
        $groups = self::GetTrainingGroups($start_date, $end_date, $branch, $focus, $allow_remote, $budget, $teachers);

        $participants = [];
        foreach ($groups as $group)
        {
            $oneGroupParticipant = self::GetParticipantsFromGroup($group, $unique, $age);
            $participants = array_merge($participants, $oneGroupParticipant);
        }

        sort($participants);
        return $participants;

    }

}