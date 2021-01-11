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
                'index document-in' => 4,
                'view document-in' => 4,
                'create document-in' => 5,
                'update document-in' => 5,
                'delete document-in' => 5,
                'actionIndex DocumentOut' => 6,
                'actionView DocumentOut' => 6,
                'actionCreate DocumentOut' => 7,
                'actionUpdate DocumentOut' => 7,
                'actionDelete DocumentOut' => 7,
                'actionIndex Order' => 8,
                'actionView Order' => 8,
                'actionCreate Order' => 9,
                'actionUpdate Order' => 9,
                'actionDelete Order' => 9,
                'actionIndex Regulation' => 10,
                'actionView Regulation' => 10,
                'actionCreate Regulation' => 11,
                'actionUpdate Regulation' => 11,
                'actionDelete Regulation' => 11,
                'actionIndex Event' => 12,
                'actionView Event' => 12,
                'actionCreate Event' => 13,
                'actionUpdate Event' => 13,
                'actionDelete Event' => 13,
                'actionIndex AS' => 14,
                'actionIndexAsType AS' => 14,
                'actionIndexCompany AS' => 14,
                'actionIndexCountry AS' => 14,
                'actionView AS' => 14,
                'actionCreate AS' => 15,
                'actionAddAsType AS' => 15,
                'actionAddCompany AS' => 15,
                'actionAddCountry AS' => 15,
                'actionUpdate AS' => 15,
                'actionDelete AS' => 15,
                'actionIndex Add' => 16,
                'actionView Add' => 16,
                'actionCreate Add' => 17,
                'actionUpdate Add' => 17,
                'actionDelete Add' => 17,
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