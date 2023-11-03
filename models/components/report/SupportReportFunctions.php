<?php

namespace app\models\components\report;

use app\models\common\AllowRemote;
use app\models\common\TeamName;
use app\models\common\Visit;
use app\models\test\work\GetGroupParticipantsCertificatWork;
use app\models\test\work\GetGroupParticipantsForeignEventParticipantWork;
use app\models\test\work\GetGroupParticipantsLessonThemeWork;
use app\models\test\work\GetGroupParticipantsTeacherGroupWork;
use app\models\test\work\GetGroupParticipantsTrainingGroupLessonWork;
use app\models\test\work\GetGroupParticipantsTrainingGroupParticipantWork;
use app\models\test\work\GetGroupParticipantsTrainingGroupWork;
use app\models\test\work\GetGroupParticipantsVisitWork;
use app\models\test\work\GetParticipantAchievementsParticipantAchievementWork;
use app\models\test\work\GetParticipantsEventWork;
use app\models\test\work\GetParticipantsTeacherParticipantBranchWork;
use app\models\test\work\GetParticipantsTeacherParticipantWork;
use app\models\test\work\GetParticipantsTeamNameWork;
use app\models\test\work\GetParticipantsTeamWork;
use app\models\work\AllowRemoteWork;
use app\models\work\BranchWork;
use app\models\work\CertificatWork;
use app\models\work\EventLevelWork;
use app\models\work\FocusWork;
use app\models\work\ForeignEventWork;
use app\models\work\LessonThemeWork;
use app\models\work\ParticipantAchievementWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TeacherParticipantBranchWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TeamNameWork;
use app\models\work\TeamWork;
use app\models\work\TrainingGroupLessonWork;
use app\models\work\TrainingGroupParticipantWork;
use app\models\work\TrainingGroupWork;
use app\models\work\VisitWork;
use yii\db\Query;

class SupportReportFunctions
{
    //|----------------------------------------------------------------------------------------------------------------
    /*|
     *|  |-------------------------|
     *|  |--Список функций класса--|
     *|  |-------------------------|
     *|
     *|  1. GetIdFromArray
     *|       Возвращает массив полей типа "id" из заданной коллекции
     *|
     *|  2. GetForeignEvents
     *|       Возвращает массив мероприятий, подходящих под заданные условия
     *|
     *|  3. GetTeacherParticipant
     *|       Возвращает массив teacher_participant, соответствующих параметрам
     *|
     *|  4. GetTeacherParticipantBranch
     *|       Возвращает массив teacher_participant_branch, соответствующих параметрам
     *|
     *|  5. GetUniqueTeacherParticipantId
     *|       Возвращает массив актов участия с уникальными участниками
     *|
     *|  6. GetParticipants (основная функция)
     *|       Возвращает массив участников мероприятий по заданным параметрам
     *|
     *|  7. GetUniqueParticipantAchievementId
     *|       Возвращает массив актов побед с уникальными участниками
     *|
     *|  8. GetParticipantAchievements (основная функция)
     *|       Возвращает массив актов побед из массива всех участников в соответствии с заданными параметрами
     *|
     *|  9. CheckAge
     *|       Проверка возраста по дате рождения и дате отсчета возраста (задается опционально)
     *|
     *|  10. GetTrainingGroups
     *|        Возвращает массив учебных групп, соответствующих заданным параметрам
     *|
     *|  11. GetParticipantsFromGroups (основная функция)
     *|        Возвращает массив учеников из массива учебных групп, соответствующих заданным параметрам
     *|
     *|  12. GetDoubleParticipantsFromGroup (основная функция)
     *|        Возвращает массив обучающихся, проходивших обучение в 2-ух и более группах
     *|
     *|  13. GetCertificatsParticipantsFromGroup
     *|          Возвращает массив обучающихся, имеющих сертификаты о завершении соответствующих учебных групп
     *|
     *|  14. GetVisits (основная функция)
     *|          Возвращает массив visit, соответствующих заданным параметрам
     *|
     */
    //|----------------------------------------------------------------------------------------------------------------



    //--Выгрузка id всех записей из массива--
    // Условие: наличие поля с именем 'id'
    static public function GetIdFromArray($array)
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
    /*
     * $test_mode - режим запуска функции (0 - боевой, 1 - тестовый)
     * [$start_date : $end_date] - Промежуток для поиска мероприятий. Мероприятие должно завершиться в заданный промежуток (границы включены)
     * $event_level - массив уровней мероприятия (региональный, федеральный...)
     */
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
    /*
     * $test_mode - режим запуска функции (0 - боевой, 1 - тестовый)
     * $eIds - подготовленный список мероприятий
     * $tpbIds - подготовленный список экземпляров класса teacher_participant_branch
     * $focus - массив направленностей
     * $allow_remote - массив форм реализации
     * $team_participants_id - список участников команд
     */
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
    /*
     * $test_mode - режим запуска функции (0 - боевой, 1 - тестовый)
     * $branch - массив отделов
     */
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
                                           $allow_remote = AllowRemoteWork::ALL,
                                           $one_event_id = null)
    {
        // Получаем подходящие мероприятия
        if ($one_event_id == null) $events = self::GetForeignEvents($test_mode, $start_date, $end_date, $event_level);
        else $events = ForeignEventWork::find()->where(['id' => $one_event_id])->all();
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

        if ($team_mode == 1)
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
            //--Получаем всех участников из teacher_participant--
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
        return [$result, $team_mode == 0 ? [] : $teamArray, $countParticipants, $tpIds, $tpmIds, $events];
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
     * $participants - список актов участия, из которого будет произведена выборка (результат работы функции GetParticipants)
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
            ParticipantAchievementWork::find()->joinWith(['teacherParticipant teacherParticipant'])
                ->where(['IN', 'teacher_participant_id', $participants[3]])
                ->andWhere(['IN', 'winner', $achieve_mode]) :
            GetParticipantAchievementsParticipantAchievementWork::find()->joinWith(['teacherParticipant teacherParticipant'])
                ->where(['IN', 'teacher_participant_id', $participants[3]])
                ->andWhere(['IN', 'winner', $achieve_mode]);

        $achievements = $unique_achieve == 0 ?
            self::GetIdFromArray($achievements->orderBy(['teacherParticipant.participant_id' => SORT_ASC])->all()) :
            self::GetUniqueParticipantAchievementId($achievements->orderBy(['teacherParticipant.participant_id' => SORT_ASC])->all());

        $achievementsTeam = self::GetIdFromArray($test_mode == 0 ?
            ParticipantAchievementWork::find()->where(['IN', 'team_name_id', $participants[1]])->andWhere(['IN', 'winner', $achieve_mode])->all() :
            GetParticipantAchievementsParticipantAchievementWork::find()->where(['IN', 'team_name_id', $participants[1]])->andWhere(['IN', 'winner', $achieve_mode])->all());

        /*$achievements = array_merge($achievements, $achievementsTeam);

        sort($achievements);
        return $achievements;*/


        $achievesAll = array_merge($achievements, $achievementsTeam);
        sort($achievesAll);
        sort($achievements);
        sort($achievementsTeam);

        return [$achievesAll, $achievements, $achievementsTeam];
    }
    //-------------------------------------------------------------------------


    //--Функция проверки возраста обучающегося--
    static public function CheckAge($birthday, $ages, $current_date)
    {
        $birthday_timestamp = strtotime($birthday);
        $current_timestamp = strtotime($current_date);
        $age = date('Y', $current_timestamp) - date('Y', $birthday_timestamp);
        if (date('md', $birthday_timestamp) > date('md', $current_timestamp)) $age--;

        return in_array($age, $ages);
    }
    //------------------------------------------

    //--Функция проверки пола обучающегося--
    static public function CheckSex($target_sex, $array_of_sex)
    {
        return in_array($target_sex, $array_of_sex);
    }
    //--------------------------------------


    //--Функция выгрузки всех учебных групп, подходящих под заданные условия--
    static public function GetTrainingGroups($test_mode, $start_date, $end_date,
                                             $branch = BranchWork::ALL,
                                             $focus = FocusWork::ALL,
                                             $allow_remote = AllowRemoteWork::ALL,
                                             $budget = ReportConst::BUDGET_ALL,
                                             $teachers = [],
                                             $date_type_selection = ReportConst::ALL_DATE_SELECTION,
                                             $network = [ReportConst::NETWORK, ReportConst::NOT_NETWORK])
    {
        $teacherGroups = $test_mode == 0 ?
            TeacherGroupWork::find()->joinWith(['trainingGroup trainingGroup'])->joinWith(['trainingGroup.trainingProgram trainingProgram'])
                ->where(in_array(ReportConst::START_IN_END_LATER, $date_type_selection) ? ['IN', 'training_group_id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])] : '0')
                ->orWhere(in_array(ReportConst::START_EARLY_END_IN, $date_type_selection) ? ['IN', 'training_group_id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])] : '0')
                ->orWhere(in_array(ReportConst::START_EARLY_END_LATER, $date_type_selection) ? ['IN', 'training_group_id', (new Query())->select('training_group.id')->from('training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])] : '0')
                ->orWhere(in_array(ReportConst::START_IN_END_IN, $date_type_selection) ? ['IN', 'training_group_id', (new Query())->select('training_group.id')->from('training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])] : '0')
                ->andWhere(['IN', 'trainingGroup.branch_id', $branch])
                ->andWhere(['IN', 'trainingGroup.budget', $budget])
                ->andWhere(['IN', 'trainingProgram.focus_id', $focus])
                ->andWhere(['IN', 'trainingProgram.allow_remote_id', $allow_remote])
                ->andWhere(['IN', 'trainingGroup.is_network', $network])
                ->andWhere($teachers == [] ? '1' : ['IN', 'teacher_group.teacher_id', $teachers])
                ->all() :
            GetGroupParticipantsTeacherGroupWork::find()->joinWith(['trainingGroup trainingGroup'])->joinWith(['trainingGroup.trainingProgram trainingProgram'])
                ->where(in_array(ReportConst::START_IN_END_LATER, $date_type_selection) ? ['IN', 'training_group_id', (new Query())->select('get_group_participants_training_group.id')->from('get_group_participants_training_group')->where(['>=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])->andWhere(['<=', 'start_date', $end_date])] : '0')
                ->orWhere(in_array(ReportConst::START_EARLY_END_IN, $date_type_selection) ? ['IN', 'training_group_id', (new Query())->select('get_group_participants_training_group.id')->from('get_group_participants_training_group')->where(['<=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])->andWhere(['>=', 'finish_date', $start_date])] : '0')
                ->orWhere(in_array(ReportConst::START_EARLY_END_LATER, $date_type_selection) ? ['IN', 'training_group_id', (new Query())->select('get_group_participants_training_group.id')->from('get_group_participants_training_group')->where(['<=', 'start_date', $start_date])->andWhere(['>=', 'finish_date', $end_date])] : '0')
                ->orWhere(in_array(ReportConst::START_IN_END_IN, $date_type_selection) ? ['IN', 'training_group_id', (new Query())->select('get_group_participants_training_group.id')->from('get_group_participants_training_group')->where(['>=', 'start_date', $start_date])->andWhere(['<=', 'finish_date', $end_date])] : '0')
                ->andWhere(['IN', 'trainingGroup.branch_id', $branch])
                ->andWhere(['IN', 'trainingGroup.budget', $budget])
                ->andWhere(['IN', 'trainingProgram.focus_id', $focus])
                ->andWhere(['IN', 'trainingProgram.allow_remote_id', $allow_remote])
                ->andWhere(['IN', 'trainingGroup.is_network', $network])
                ->andWhere($teachers == [] ? '1' : ['IN', 'teacher_id', $teachers])
                ->all();

        $tgId = [];
        foreach ($teacherGroups as $one) $tgId[] = $one->training_group_id;

        $result = $test_mode == 0 ?
            TrainingGroupWork::find()->where(['IN', 'id', $tgId])->all() :
            GetGroupParticipantsTrainingGroupWork::find()->where(['IN', 'id', $tgId])->all();

        return $result;
    }
    //------------------------------------------------------------------------


    //-|---------------------------------------------------------------------------|-
    //-| Функция для получения обучающихся из учебных групп по заданным параметрам |-
    //-|---------------------------------------------------------------------------|-
    /*
     * $test_mode - режим запуска функции (0 - боевой, 1 - тестовый)
     * $unique - тип выгрузки обучающихся (0 - все, 1 - уникальные)
     * $age - массив возрастов обучающихся
     * $current_date - дата, относительно которой рассчитывается возраст обучающихся (по умолчанию - текущая дата сервера)
     */
    static public function GetParticipantsFromGroups($test_mode, $groups,
                                                    $unique = 0,
                                                    $age = ReportConst::AGES_ALL,
                                                    $current_date = null,
                                                    $sex = [ReportConst::MALE, ReportConst::FEMALE])
    {
        if ($current_date == null) $current_date = date('Y-m-d'); // если не передано значение, то выставляем текущую дату

        $groupIds = self::GetIdFromArray($groups);

        //--Находим подходящих по группе обучающихся--
        $participants = $test_mode == 0 ?
            TrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['IN', 'training_group_id', $groupIds])->andWhere(['IN', 'participant.sex', $sex])->orderBy(['participant_id' => SORT_ASC, 'id' => SORT_ASC])->all() :
            GetGroupParticipantsTrainingGroupParticipantWork::find()->joinWith(['participant participant'])->where(['IN', 'training_group_id', $groupIds])->andWhere(['IN', 'participant.sex', $sex])->orderBy(['participant_id' => SORT_ASC, 'id' => SORT_ASC])->all();
        //--------------------------------------------

        //--Производим отбор по возрасту и удаляем дубликаты (при необходимости)--
        $currentParticipant = $participants[0]->participant_id; // текущий id обучающегося (для уникального режима)
        $resultParticipant = $unique == 0 ? [] : [$participants[0]]; // если считаем уникальных - то первого сразу заносим в список
        foreach ($participants as $participant)
        {
            if ($age !== ReportConst::AGES_ALL)
                if (!self::CheckAge($participant->participant->birthdate, $age, $current_date))
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
        sort($resultParticipant);
        return $resultParticipant;
    }
    //---------------------------------------------------------------------------------------


    //-|------------------------------------------------------------------------|-
    //-| Функция для получения обучающихся по более чем в одной учебной группе  |-
    //-|------------------------------------------------------------------------|-
    /*
     * $test_mode - режим запуска функции (0 - боевой, 1 - тестовый)
     * $groups - список групп для выборки
     * $age - массив возрастов обучающихся
     * $current_date - дата, относительно которой рассчитывается возраст обучающихся (по умолчанию - текущая дата сервера)
     */
    static public function GetDoubleParticipantsFromGroup($test_mode, $groups,
                                                          $age = ReportConst::AGES_ALL,
                                                          $current_date = null)
    {
        if ($current_date == null) $current_date = date('Y-m-d'); // если не передано значение, то выставляем текущую дату

        $groupIds = self::GetIdFromArray($groups);

        //--Находим подходящих по группе обучающихся--
        $participants = $test_mode == 0 ?
            TrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groupIds])->orderBy(['participant_id' => SORT_ASC, 'id' => SORT_ASC])->all() :
            GetGroupParticipantsTrainingGroupParticipantWork::find()->where(['IN', 'training_group_id', $groupIds])->orderBy(['participant_id' => SORT_ASC, 'id' => SORT_ASC])->all();
        //--------------------------------------------

        //--Производим отбор по возрасту и удаляем дубликаты (при необходимости)--
        $currentParticipant = $participants[0]->participant_id; // текущий id обучающегося (для уникального режима)
        $resultParticipant = [];
        $repeatFlag = false; // флаг для отслеживания повторяющихся обучающихся
        foreach ($participants as $participant)
        {
            if ($age !== ReportConst::AGES_ALL)
                if (!self::CheckAge($participant->participant->birthdate, $age, $current_date))
                    continue;

            if ($participant->participant_id == $currentParticipant && !$repeatFlag)
            {
                // Добавляем повторяющегося обучающегося
                $repeatFlag = true;
                $resultParticipant[] = $participant->id;
                //--------------------------------------
            }
            else if ($participant->participant_id !== $currentParticipant)
            {
                // Сброс флага повторяющегося обучающегося
                $repeatFlag = false;
                $currentParticipant = $participant->participant_id;
                //----------------------------------------
            }
        }
        //------------------------------------------------------------------------
        sort($resultParticipant);
        $result = $test_mode == 0 ?
            TrainingGroupParticipantWork::find()->where(['IN', 'id', $resultParticipant])->orderBy(['participant_id' => SORT_ASC])->all() :
            GetGroupParticipantsTrainingGroupParticipantWork::find()->where(['IN', 'id', $resultParticipant])->orderBy(['participant_id' => SORT_ASC])->all();

        return $result;
    }


    //-|------------------------------------------------------------------------------------------|-
    //-| Функция для получения обучающихся, получивших сертификат об успешном окончании обучения  |-
    //-|------------------------------------------------------------------------------------------|-
    /*
     * $test_mode - режим запуска функции (0 - боевой, 1 - тестовый)
     * $participants - список обучающихся для выборки
     */
    static public function GetCertificatsParticipantsFromGroup($test_mode, $participants)
    {
        $pIds = self::GetIdFromArray($participants);

        return $test_mode == 0 ?
            CertificatWork::find()->where(['IN', 'training_group_participant_id', $pIds])->all() :
            GetGroupParticipantsCertificatWork::find()->where(['IN', 'training_group_participant_id', $pIds])->all();
    }


    //-|----------------------------------------------------------------|-
    //-| Функция для получения человеко-часов по заданным параметрам    |-
    //-|----------------------------------------------------------------|-
    /*
     * $test_mode - режим запуска функции (0 - боевой, 1 - тестовый)
     * $participants - список обучающихся для выборки
     * [$start_date : $end_date] - промежуток для поиска учебных занятий
     * $visit_type - тип учета явок
     * $teachers - массив id педагогов, проводящих занятия
     */
    static public function GetVisits($test_mode, $participants, $start_date, $end_date, $visit_type, $teachers = null)
    {
        $pIds = [];
        foreach ($participants as $participant) $pIds[] = $participant->participant_id;

        $gIds = []; //все группы из $participants для дальнейшего поиска занятий training_group_lessons
        foreach ($participants as $one) $gIds[] = $one->training_group_id;

        if ($teachers !== null)
        {
            $teachIds = [];
            $lessonThemes = $test_mode == 0 ?
                LessonThemeWork::find()->where(['IN', 'teacher_id', $teachers])->all() :
                GetGroupParticipantsLessonThemeWork::find()->where(['IN', 'teacher_id', $teachers])->all();
            foreach ($lessonThemes as $one)
                $teachIds[] = $one->training_group_lesson_id;
        }
        else
            $teachIds = null;

        $groupLessons = $test_mode == 0 ?
            TrainingGroupLessonWork::find()->where(['IN', 'training_group_id', $gIds])->andWhere($teachIds == null ? '1' : ['IN', 'id', $teachIds])->all() :
            GetGroupParticipantsTrainingGroupLessonWork::find()->where(['IN', 'training_group_id', $gIds])->andWhere($teachIds == null ? '1' : ['IN', 'id', $teachIds])->all();

        $glIds = self::GetIdFromArray($groupLessons);

        $visits = $test_mode == 0 ?
            VisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])
                    ->where(['IN', 'foreign_event_participant_id', $pIds])
                    ->andWhere(['IN', 'training_group_lesson_id', $glIds])
                    ->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])
                    ->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])
                    ->andWhere(['IN', 'status', $visit_type])->all() :
            GetGroupParticipantsVisitWork::find()->joinWith(['trainingGroupLesson trainingGroupLesson'])
                    ->where(['IN', 'foreign_event_participant_id', $pIds])
                    ->andWhere(['IN', 'training_group_lesson_id', $glIds])
                    ->andWhere(['>=', 'trainingGroupLesson.lesson_date', $start_date])
                    ->andWhere(['<=', 'trainingGroupLesson.lesson_date', $end_date])
                    ->andWhere(['IN', 'status', $visit_type])->all();

        $visits = self::GetIdFromArray($visits);
        sort($visits);
        return $visits;
    }
}