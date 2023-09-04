<?php


namespace app\models\extended;


use app\models\common\TrainingGroup;
use app\models\common\User;

class AccessTrainingGroup extends \yii\base\Model
{
    static public function CheckAccess($id, $group_id)
    {
        if ($group_id == null)
            return true;
        $user = User::find()->where(['id' => $id])->one();
        if ($user == null)
            return false;
        return TrainingGroup::find()->where(['teacher_id' => $user->aka])->andWhere(['id' => $group_id])->one() !== null;
    }

    static public function GetTeacherGroups($id)
    {
        $groups = [new TrainingGroup];
        $user = User::find()->where(['aka' => $id])->one();
        if ($user == null) return $groups;
        $groups = TrainingGroup::find()->where(['teacher_id' => $user->aka])->all();
        return $groups;
    }
}