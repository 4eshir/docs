<?php


namespace app\models\components;


use app\models\work\AccessLevelWork;
use app\models\work\PeopleWork;
use app\models\work\RoleFunctionRoleWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TrainingGroupWork;
use app\models\work\UserRoleWork;
use app\models\work\UserWork;
use yii\db\ActiveQuery;
//vdvdvdv

class RoleBaseAccess
{
    /*
     * двумерный массив прав доступа
     * вида [контроллер=>[экшн=>role_function_id,...]]
     * для одинаковых контроллеров но разных разделов (приказы, положения)
     * вида [контроллер=>[экшн=>[role_function_id №1, role_function_id №2...],...]]
     */
    private static $access = [
        //Справочники (кроме участников деятельности)
        "auditorium" => [
            "index" => 43,
            "create" => 44,
            "update" => 44,
            "delete" => 44,
            "view" => 43,
            "find-model" => 43,
            "get-file" => 43,
        ],
        "branch" => [
            "index" => 43,
            "create" => 44,
            "update" => 44,
            "delete" => 44,
            "view" => 43,
            "find-model" => 43,
            "delete-auditorium" => 44,
        ],
        "company" => [
            "index" => 43,
            "create" => 44,
            "update" => 44,
            "delete" => 44,
            "view" => 43,
            "find-model" => 43,
        ],
        "event-external" => [
            "index" => 43,
            "create" => 44,
            "update" => 44,
            "delete" => 44,
            "view" => 43,
            "find-model" => 43,
        ],
        "event-form" => [
            "index" => 43,
            "create" => 44,
            "update" => 44,
            "delete" => 44,
            "view" => 43,
            "find-model" => 43,
        ],
        "people" => [
            "index" => 43,
            "create" => 44,
            "update" => 44,
            "delete" => 44,
            "view" => 43,
            "find-model" => 43,
            "delete-position" => 44,
        ],
        "position" => [
            "index" => 43,
            "create" => 44,
            "update" => 44,
            "delete" => 44,
            "view" => 43,
            "find-model" => 43,
        ],
        "responsibility-type" => [
            "index" => 43,
            "create" => 44,
            "update" => 44,
            "delete" => 44,
            "view" => 43,
            "find-model" => 43,
        ],
        "thematic-direction" => [
            "index" => 56,
            "create" => 57,
            "update" => 57,
            "delete" => 57,
            "view" => 56,
        ],
        //--------------------------------------------

        //Контейнеры
        "container" => [
            "index" => 63,
            "create" => 61,
            "update" => 61,
            "delete" => 61,
            "view" => 63,
            //"subcat" => 63,
            "delete-object" => 63,
        ],
        //---------------------

        // Договора по мат.ценностям
        "contract" => [
            "index" => 69,
            "create" => 70,
            "update" => 70,
            "delete" => 71,
            "view" => 69,
            "get-file" => 69,
            "delete-file" => 70,
        ],

        // Документы о поступлении
        "invoice" => [
            "index" => 69,
            "create" => 70,
            "update" => 70,
            "delete" => 71,
            "view" => 69,
            "delete-entry" => 70,
            "update-entry" => 70,
            "update-object" => 70,
            "delete-entry-doc" => 70,
            "delete-object" => 70,
            "subcat" => 69,
            "get-file" => 69,
            "delete-file" => 70,
            "get-entry-file" => 69,
            "subattr" => 69,
        ],
        
        //-------------------------------------

        //Генерация сертификаты
        "certificat" => [
            "main-index" => 51,
            "index" => 51,
            "view" => 51,
            "create" => 53,
            "delete" => 54,
            "download" => 51,
        ],

        "certificat-templates" => [
            "index" => 52,
            "view" => 52,
            "create" => 55,
            "update" => 55,
            "delete" => 55,
            "getFile" => 52,
        ],

        //---------------------

        //Приказы
        //0 - основная деятельность; 1 - учебные
        "document-order" => [
            "index" => [31, 23],
            "view" => [31, 23],
            "create" => [32, 24],
            "create-reserve" => [32, 24],
            "update" => [32, 24],
            "delete-expire" => [32, 24],
            "delete-file" => [32, 24],
            "get-file" => [31, 23],
            "delete-responsible" => [32, 24],
            "delete" => [32, 24],
            "subattr" => [31, 23],
            "find-model" => [31, 23],
            "amnesty" => [32, 24],
            "generation-word" => [32, 24],
            "generation-protocol" => [32, 24],
            "delete-participant" => [31, 23],
            "update-participant" => [31, 23],
        ],
        //----------------------------------------

        //Исходящая документация
        "docs-out" => [
            "index" => 29,
            "create" => 30,
            "create-reserve" => 30,
            "update" => 30,
            "delete" => 30,
            "delete-file" => 30,
            "view" => 29,
            "find-model" => 29,
            "subcat" => 29,
            "positions" => 29,
            "get-file" => 29,
        ],
        //-----------------------

        //Входящая документация
        "document-in" => [
            "index" => 27,
            "create" => 28,
            "create-reserve" => 28,
            "update" => 28,
            "delete" => 28,
            "delete-file" => 28,
            "view" => 27,
            "find-model" => 27,
            "subcat" => 27,
            "positions" => 27,
            "get-file" => 27,
        ],
        //----------------------

        //Мероприятия
        "event" => [
            "index" => 37,
            "create" => 38,
            "update" => 38,
            "delete" => 38,
            "delete-file" => 38,
            "delete-external-event" => 38,
            "view" => 37,
            "find-model" => 37,
            "get-file" => 37,
        ],
        //-----------

        //Участие в мероприятиях
        "foreign-event" => [
            "index" => 39,
            "create" => 40,
            "update" => 40,
            "delete" => 40,
            "delete-file" => 40,
            "delete-achievement" => 40,
            "update-achievement" => 40,
            "delete-participant" => 40,
            "update-participant" => 40,
            "view" => 39,
            "find-model" => 39,
            "get-file" => 39,
            "form-order" => 40,
        ],
        //----------------------

        //Участники образовательной деятельности
        "foreign-event-participants" => [
            "index" => 17,
            "create" => 18,
            "update" => 18,
            "file-load" => 18,
            "check-correct" => 18,
            "delete" => 19,
            "view" => 17,
            "find-model" => 17,
            "merge-participant" => 50,
            "info" => 50,
        ],
        //--------------------------------------

        //Учет ответственности работников
        "local-responsibility" => [
            "index" => 41,
            "create" => 42,
            "update" => 42,
            "delete" => 42,
            "delete-file" => 42,
            "view" => 41,
            "subcat" => 41,
            "find-model" => 41,
            "get-file" => 41,
        ],
        //-------------------------------

        //Материальные ценности
        "material-object" => [
            "index" => 62,
            "create" => 60,
            "update" => 60,
            "delete" => 60,
            "view" => 62,
            "subcat" => 62,
        ],
        //---------------------

        //Положения
        //0 - положения, инструкции, правила; 1 - о мероприятиях
        "regulation" => [
            "index" => [35, 33],
            "create" => [36, 34],
            "update" => [36, 34],
            "delete" => [36, 34],
            "delete-file" => [36, 34],
            "view" => [35, 33],
            "find-model" => [35, 33],
            "get-file" => [35, 33],
        ],
        //------------------------------------------------------

        //Отчеты
        "report" => [
            "man-hours-report" => [25, 26],
            "foreign-event-report" => [25, 26],
            "report-result" => [25, 26],
            "get-full-report" => [25, 26],
        ],
        "report-form" => [
            "index" => [25, 26],
            "effective-contract" => [25, 26],
            "do-dop-1" => [25, 26],
            "gz" => [25, 26],
            "do" => [25, 26],
            "teacher" => [25, 26],
        ],
        //------

        //Роли
        "role" => [
            "index" => 48,
            "create" => 48,
            "update" => 48,
            "delete" => 48,
            "view" => 48,
            "find-model" => 48,
        ],
        //----

        //Учебные группы
        //$special = "group", каждому экшну соответствует массив прав доступа, для доступа достаточно одного совпадения
        "training-group" => [
            "index" => [2, 3, 4],
            "create" => [1],
            "update" => [5, 6, 7],
            "delete" => [8, 9],
            "delete-participant" => [5, 6, 7],
            "remand-participant" => [5, 6, 7],
            "unremand-participant" => [5, 6, 7],
            "update-participant" => [5, 6, 7],
            "update-lesson" => [5, 6, 7],
            "delete-lesson" => [5, 6, 7],
            "delete-order" => [5, 6, 7],
            "delete-teacher" => [5, 6, 7],
            "view" => [2, 3, 4],
            "download-excel" => [2, 3, 4],
            "delete-file" => [5, 6, 7],
            "get-file" => [2, 3, 4],
            "subcat" => [2, 3, 4],
            "parse" => [5, 6, 7],
            "get-kug" => [2, 3, 4],
            "archive" => [8, 9], //пока что архивировать может тот же, кто и удаляет
            "amnesty" => [8, 9], //пока амнистию может давать тот же, кто и удаляет
            "download-journal" => [8, 9],
            "delete-theme" => [5, 6, 7],
            "decline-theme" => [5, 6, 7],
            "confirm-theme" => [5, 6, 7],
            "delete-expert" => [5, 6, 7],
            "get-archive" => [51, 52],
            "send-certificats" => [51, 52],
        ],
        //--------------

        //Учебные программы
        "training-program" => [
            "index" => 20,
            "view" => 20,
            "create" => 21,
            "update" => 21,
            "update-plan" => 21,
            "delete" => 21,
            "saver" => 21,
            "actual" => 21,
            "find-model" => 20,
            "get-file" => 20,
            "delete-file" => 21,
            "delete-author" => 21,
            "delete-plan" => 21,
            "amnesty" => 21,
        ],
        //-----------------

        //Пользователи
        "user" => [
            "index" => 45,
            "create" => 45,
            "update" => 46,
            "delete" => 46,
            "delete-role" => 46,
            "view" => 45,
            "find-model" => 45,
        ],
        //------------

    ];

    //----------------------------------------------------

    //Проверка одиночного права доступа (без привязки к экшну и контроллеру)
    public static function CheckSingleAccess($userId, $accessId)
    {
        $userAccess = UserRoleWork::find()->where(['user_id' => $userId])->all();
        $accesses = AccessLevelWork::find()->where(['user_id' => $userId])->all();
        $accessArray = [];
        foreach ($userAccess as $access)
        {
            $functions = RoleFunctionRoleWork::find()->where(['role_id' => $access->role_id])->all();
            foreach ($functions as $function)
                if ($function->role_function_id == $accessId)
                    return true;
            if ($accesses !== null)
                foreach ($accesses as $acc)
                    if ($acc->role_function_id == $accessId)
                        return true;
        }
        return false;
    }

    //Проверка прав доступа для совершения CRUD-операции (с учетом экшна и контроллера)
    public static function CheckAccess($controllerName, $actionName, $userId, $special = -1, $groupId = 0)
    {
        $userAccess = UserRoleWork::find()->where(['user_id' => $userId])->all();
        $accesses = AccessLevelWork::find()->where(['user_id' => $userId])->all();
        $accessArray = [];
        foreach ($userAccess as $access)
        {
            $functions = RoleFunctionRoleWork::find()->where(['role_id' => $access->role_id])->all();
            foreach ($functions as $function)
                $accessArray[] = $function->role_function_id;
            if ($accesses !== null)
                foreach ($accesses as $acc)
                    $accessArray[] = $acc->role_function_id;
        }
        $allow = false;
        for ($i = 0; $i < count($accessArray); $i++){
            if ($special == 1 || $special == 2) //специальный раздел для приказов и мероприятий (основные/учебные...)
            {
                if ($accessArray[$i] == RoleBaseAccess::$access[$controllerName][$actionName][$special - 1])
                    $allow = true;
            }
            else if ($special == "group") //специальный раздел для групп и отчетов (подробнее см. в массиве $access)
            {
                for ($j = 0; $j < count(RoleBaseAccess::$access[$controllerName][$actionName]); $j++)
                    if ($accessArray[$i] == RoleBaseAccess::$access[$controllerName][$actionName][$j]
                        && (RoleBaseAccess::IsGroupAccessAllowed($userId, $groupId) || $groupId == 0))
                        $allow = true;
            }
            else //обычный режим
            {
                if ($accessArray[$i] == RoleBaseAccess::$access[$controllerName][$actionName])
                    $allow = true;
            }
        }
        return $allow;
    }

    public static function CheckRole($userId, $role)
    {
        $roles = UserRoleWork::find()->where(['user_id' => $userId])->andWhere(['role_id' => $role])->all();
        if (count($roles) == 0) return false;
        else return true;
    }

    //Выгрузка групп по роли пользователя
    public static function getGroupsByRole($userId)
    {
        $user = UserWork::find()->where(['id' => $userId])->one();
        $userAccess = UserRoleWork::find()->where(['user_id' => $userId])->all();
        $accessArray = [];
        foreach ($userAccess as $access)
        {
            $functions = RoleFunctionRoleWork::find()->where(['role_id' => $access->role_id])->all();
            foreach ($functions as $function)
                $accessArray[] = $function->role_function_id;
        }

        $groupArray = TrainingGroupWork::find();
        $f = false;
        if (array_search(2, $accessArray) || array_search(5, $accessArray)) //свои учебные группы
        {
            $teachers = TeacherGroupWork::find()->where(['teacher_id' => $user->aka])->all();
            $tempId = [];
            foreach ($teachers as $teacher) $tempId[] = $teacher->training_group_id;
            $groupArray = $groupArray->where(['IN', 'training_group.id', $tempId]);
            $f = true;
        }
        if (array_search(3, $accessArray) || array_search(6, $accessArray)) //учебные группы своего отдела
        {
            $aka = PeopleWork::find()->where(['id' => $user->aka])->one();
            $groupArray = $groupArray->orWhere(['branch_id' => $aka->branch_id]);
            $f = true;
        }
        if (array_search(4, $accessArray) || array_search(7, $accessArray)) //все учебные группы
        {
            $groupArray = TrainingGroupWork::find();
            $f = true;
        }

        if (!$f)
            return TrainingGroupWork::find()->where(['training_group.id' => -1]);
        return $groupArray;
    }

    public static function IsGroupAccessAllowed($userId, $groupId)
    {
        $groups = RoleBaseAccess::getGroupsByRole($userId)->all();

        foreach ($groups as $group)
            if ($group->id == $groupId)
                return true;

        return false;
    }
}