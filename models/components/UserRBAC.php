<?php

namespace app\models\components;

use app\models\common\Access;
use app\models\common\AccessLevel;
use app\models\common\TrainingGroup;
use app\models\common\User;
use Yii;

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
                'delete-as-type as-admin' => 15,
                'add-company as-admin' => 15,
                'delete-company as-admin' => 15,
                'add-license as-admin' => 15,
                'delete-license as-admin' => 15,
                'add-country as-admin' => 15,
                'delete-country as-admin' => 15,
                'refresh-license as-admin' => 15,
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
                'create training-group' => 26,
                'delete training-group' => 27,
                'man-hours-report report' => 28,
                'report-result report' => 28,
            );
        }

    }

    public static function IsAccess($user_id, $access_id)
    {
        return AccessLevel::find()->where(['user_id' => $user_id])->andWhere(['access_id' => $access_id])->one() !== null;
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

    public static function CheckAccessGroupListEdit($user_id, $group_id)
    {
        $user = User::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
        $groups = TrainingGroup::find()->where(['teacher_id' => $user->aka])->all();
        $newGroups_id = [];
        if (UserRBAC::IsAccess(Yii::$app->user->identity->getId(), 23)) //доступ на редактирование ВСЕХ групп
        {
            $groups = TrainingGroup::find()->all();
            foreach ($groups as $group) $newGroups_id[] = $group->id;
        }
        else if (UserRBAC::IsAccess(Yii::$app->user->identity->getId(), 25)) //доступ на редактирование групп СВОЕГО ОТДЕЛА
        {
            $branchs = \app\models\common\PeoplePositionBranch::find()->select('branch_id')->distinct()->where(['people_id' => $user->aka])->all();
            if ($branchs !== null)
            {
                $branchs_id = [];
                foreach ($branchs as $branch) $branchs_id[] = $branch->branch_id;
                $groups_id = \app\models\common\TrainingGroupLesson::find()->select('training_group_id')->distinct()->where(['in', 'branch_id', $branchs_id])->all();

                $newGroups_id = [];
                foreach ($groups_id as $group) $newGroups_id[] = $group->training_group_id;
            }
        }

        $teachers = \app\models\common\TeacherGroup::find()->select('training_group_id')->distinct()->where(['teacher_id' => $user->aka])->all();
        foreach ($teachers as $teacher) $newGroups_id[] = $teacher->training_group_id;

        return in_array($group_id, $newGroups_id);
    }

    /*
    public static function GetGroupList($user_id, $access_id)
    {
        $user = User::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
        $groups = TrainingGroup::find()->where(['teacher_id' => $user->aka])->all();
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), 'index', 'training-group'))
        {
            $groups = TrainingGroup::find()->all();
        }
        else
        {
            $groups = array_merge($groups, UserRBAC::GetAccessGroupList(Yii::$app->user->identity->getId(), 26));
        }
        if ($groups !== null)
            $items =  \yii\helpers\ArrayHelper::map($groups,'id','number');
        else
        {
            $tgroups = \app\models\common\TeacherGroup::find()->where(['teacher_id' => $user->aka])->all();
            $tgroups = \yii\helpers\ArrayHelper::map($tgroups, 'id', 'training_group_id');
            $groups = TrainingGroup::find()->where(['in', 'id', $tgroups])->all();
            $items = \yii\helpers\ArrayHelper::map($groups, 'id', 'number');
        }
        return $groups;
    }

    public static function CheckGroupAccess($user_id, $group_id)
    {
        if ($group_id == null) return true;
        $user = User::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
        $groups = TrainingGroup::find()->where(['teacher_id' => $user->aka])->all();
        if (!UserRBAC::CheckAccess(Yii::$app->user->identity->getId(), 'index', 'training-group'))
        {
            $groups = TrainingGroup::find()->all();
        }
        else
        {
            $groups = array_merge($groups, UserRBAC::GetAccessGroupList(Yii::$app->user->identity->getId(), 26));
        }
        if ($groups !== null)
            $items =  \yii\helpers\ArrayHelper::map($groups,'id','number');
        else
        {
            $tgroups = \app\models\common\TeacherGroup::find()->where(['teacher_id' => $user->aka])->all();
            $tgroups = \yii\helpers\ArrayHelper::map($tgroups, 'id', 'training_group_id');
            $groups = TrainingGroup::find()->where(['in', 'id', $tgroups])->all();
            $items = \yii\helpers\ArrayHelper::map($groups, 'id', 'number');
        }
        $targetGroup = TrainingGroup::find()->where(['in', 'number', $items])->one();
        return $targetGroup !== null;
    }
    */
}