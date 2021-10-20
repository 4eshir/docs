<?php


namespace app\models\components;


use app\models\work\RoleFunctionRoleWork;
use app\models\work\UserRoleWork;

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
        //--------------------------------------------

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
            "delete-participant" => 40,
            "update-participant" => 40,
            "view" => 39,
            "find-model" => 39,
            "get-file" => 39,
        ],
        //----------------------

        //Участники образовательной деятельности
        "foreign-event-participant" => [
            "index" => 17,
            "create" => 18,
            "update" => 18,
            "file-load" => 18,
            "check-correct" => 18,
            "delete" => 20,
            "view" => 17,
            "find-model" => 17,
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

        //Учебные группы НЕ ГОТОВО
        "training-group" => [
            "index" => 41,
            "create" => 42,
            "update" => 42,
            "delete" => 42,
            "delete-participant" => 42,
            "remand-participant" => 42,
            "unremand-participant" => 42,
            "update-participant" => 42,
            "update-lesson" => 42,
            "delete-lesson" => 42,
            "delete-order" => 42,
            "delete-teacher" => 42,
            "view" => 41,
            "delete-file" => 41,
            "get-file" => 41,
            "subcat" => 41,
            "parse" => 41,
            "archive" => 41,
            "amnesty" => 41,
        ],
        //--------------

        //Учебные программы
        "training-program" => [
            "index" => 20,
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
            "index" => 47,
            "create" => 45,
            "update" => 47,
            "delete" => 46,
            "delete-role" => 47,
            "view" => 47,
            "find-model" => 47,
        ],
        //------------
    ];

    //----------------------------------------------------

    //Проверка прав доступа для совершения CRUD-операции
    public static function CheckAccess($controllerName, $actionName, $userId, $special = null)
    {
        $userAccess = UserRoleWork::find()->where(['user_id' => $userId])->all();
        $accessArray = [];
        foreach ($userAccess as $access)
        {
            $functions = RoleFunctionRoleWork::find()->where(['role_id' => $access->role_id])->all();
            foreach ($functions as $function)
                $accessArray[] = $function->role_function_id;
        }
        $allow = false;
        for ($i = 0; $i < count($accessArray); $i++)
            if ($special !== null)
                if ($accessArray[$i] == RoleBaseAccess::$access[$controllerName][$actionName][$special])
                    $allow = true;
            else
                if ($accessArray[$i] == RoleBaseAccess::$access[$controllerName][$actionName])
                    $allow = true;

        return $allow;
    }
}