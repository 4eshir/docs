<?php

namespace app\models\components;

use app\models\common\Access;
use app\models\common\AccessLevel;

class UserRBAC
{
    private static $accessArray = null;

    private static function CreateAccessArray()
    {
        if (UserRBAC::$accessArray == null)
        {
            UserRBAC::$accessArray = array(
                'create user' => 1,
                'index user' => 2,
                'view user' => 2,
                'update user' => 3,
                'delete user' => 3,
                'index docs-out' => 4,
                'view docs-out' => 4,
                'create docs-out' => 5,
                'update docs-out' => 5,
                'delete docs-out' => 5,
                'index document-in' => 6,
                'view document-in' => 6,
                'create document-in' => 7,
                'update document-in' => 7,
                'delete document-in' => 7,
                'index document-order' => 8,
                'view document-order' => 8,
                'create document-order' => 9,
                'update document-order' => 9,
                'delete document-order' => 9,
                'index regulation' => 10,
                'view regulation' => 10,
                'create regulation' => 11,
                'update regulation' => 11,
                'delete regulation' => 11,
                'index event' => 12,
                'view event' => 12,
                'create event' => 13,
                'update event' => 13,
                'delete event' => 13,
                'index as-admin' => 14,
                'index-as-type as-admin' => 14,
                'index-company as-admin' => 14,
                'index-country as-admin' => 14,
                'index-license as-admin' => 14,
                'view as-admin' => 14,
                'create as-admin' => 15,
                'add-as-type as-admin' => 15,
                'add-company as-admin' => 15,
                'add-country as-admin' => 15,
                'update as-admin' => 15,
                'delete as-admin' => 15,
                'index Add' => 16,
                'view Add' => 16,
                'create Add' => 17,
                'update Add' => 17,
                'delete Add' => 17,
                'index foreign-event' => 18,
                'view foreign-event' => 18,
                'create foreign-event' => 19,
                'update foreign-event' => 19,
                'delete foreign-event' => 19,
                'index training-program' => 20,
                'view training-program' => 20,
                'create training-program' => 21,
                'update training-program' => 21,
                'delete training-program' => 21,
                'index training-group' => 22,
                'view training-group' => 22,
                'create training-group' => 23,
                'update training-group' => 23,
                'delete training-group' => 23,
                'index journal' => 24,
                'index-edit journal' => 25,
            );
        }

    }

    public static function CheckAccess($user_id, $action_type, $subsystem)
    {
        UserRBAC::CreateAccessArray();
        $access = AccessLevel::find()->where(['user_id' => $user_id])->all();
        foreach ($access as $accessOne)
        {
            if ($accessOne->access_id == UserRBAC::$accessArray[$action_type.' '.$subsystem])
                return true;
        }
        return false;
    }
}