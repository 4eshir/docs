<?php

namespace app\models\work;

use app\models\common\Errors;
use Yii;
use yii\helpers\Html;


class ErrorsWork extends Errors
{
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'number' => 'Код ошибки',
            'name' => 'Наименование ошибки',
        ];
    }

    private function ErrorsToGroupAndJournal($user, $critical)
    {
        $result = '';
        $groups = '';
        if (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 14))
        {
            $groups = TrainingGroupWork::find()->all();
        }
        else if (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 13))
        {
            $branch = PeopleWork::find()->where(['id' => $user->aka])->one()->branch->id;
            $groups = TrainingGroupWork::find()->where(['branch_id' => $branch])->all();
        }
        else if (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 12))
        {
            // тут должна быть выборка только учебных групп одного конкретного препода
            $groups = TrainingGroupWork::find()->joinWith(['teacherGroups teacherGroups'])->where(['teacherGroups.teacher_id' => $user->aka])->all();
        }

        if ($groups !== '')
        {
            $result .= '<table class="table table-bordered">';
            $result .= '<h4 style="text-align: center;"><u>Ошибки в учебных группах</u></h4>';
            $result .= '<thead>';
            $result .= '<th style="vertical-align: middle;">Код проблемы</th>';
            $result .= '<th style="vertical-align: middle;">Описание проблемы</th>';
            $result .= '<th style="vertical-align: middle;">Место возникновения</th>';
            $result .= '</thead>';

            $result .= '<tbody>';
            foreach ($groups as $group)
            {
                if ($critical == 0)
                    $errorsList = GroupErrorsWork::find()->where(['training_group_id' => $group->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
                else
                    $errorsList = GroupErrorsWork::find()->where(['training_group_id' => $group->id, 'time_the_end' => NULL, 'amnesty' => NULL, 'critical' => 1])->all();

                foreach ($errorsList as $error)
                {
                    if ($error->critical == 1)
                        $result .= '<tr style="background-color: #FCF8E3;">';
                    else
                        $result .= '<tr>';
                    $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
                    $result .= '<th style="text-align: left;">' . $errorName->number . "</th>";
                    $result .= '<td>' . $errorName->name . '</td>';
                    $result .= '<td>' . Html::a($group->number, \yii\helpers\Url::to(['training-group/view', 'id' => $group->id])) . '</td>';
                    $result .= '</tr>';
                }
            }
            $result .= '</tbode></table>';
        }
        return $result;
    }

    private function ErrorsToTrainingProgram($user, $actual)
    {
        $result = '';
        $programs = '';
        if (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 16))
        {
            if ($actual == 0)
                $programs = TrainingProgramWork::find()->all();
            else
                $programs = TrainingProgramWork::find()->where(['actual' => 1])->all();
        }
        else if (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 15))
        {
            $branch = PeopleWork::find()->where(['id' => $user->aka])->one()->branch->id;
            if ($actual == 0)
                $programs = TrainingProgramWork::find()->joinWith(['branchPrograms branchPrograms'])->where(['branchPrograms.branch_id' => $branch])->all();
            else
                $programs = TrainingProgramWork::find()->joinWith(['branchPrograms branchPrograms'])->where(['branchPrograms.branch_id' => $branch])->andWhere(['actual' => 1])->all();
        }

        if ($programs !== '')
        {
            $result .= '<table class="table table-bordered"><h4 style="text-align: center;"><u>Ошибки в образовательных программах</u></h4>';
            $result .= '<thead>';
            $result .= '<th style="vertical-align: middle;">Код проблемы</th>';
            $result .= '<th style="vertical-align: middle;">Описание проблемы</th>';
            $result .= '<th style="vertical-align: middle;">Место возникновения</th>';
            $result .= '</thead>';
            $result .= '<tbody>';

            foreach ($programs as $program)
            {
                $errorsList = ProgramErrorsWork::find()->where(['training_program_id' => $program->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
                foreach ($errorsList as $error)
                {
                    if ($error->critical == 1)
                        $result .= '<tr style="background-color: #FCF8E3;">';
                    else
                        $result .= '<tr>';
                    $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
                    $result .= '<th style="text-align: left;">' . $errorName->number . "</th>";
                    $result .= '<td>' . $errorName->name . '</td>';
                    $result .= '<td>' . Html::a($program->name, \yii\helpers\Url::to(['training-program/view', 'id' => $program->id])) . '</td>';
                    $result .= '</tr>';
                }
            }
            $result .= '</tbody></table>';
        }
        return $result;
    }

    public function ErrorsElectronicJournalSubsystem($user, $critical)
    {
        $result = $this->ErrorsToGroupAndJournal($user, $critical);
        $result .= '<br><br>';
        $result .= $this->ErrorsToTrainingProgram($user, $critical);
        return $result;
    }
}
