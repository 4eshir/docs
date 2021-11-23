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
            $result .= '<table id="training-group" class="table table-bordered">';
            $result .= '<h4 style="text-align: center;"><u><a onclick="hide(0)"> Ошибки в учебных группах</a></u></h4>';
            $result .= '<thead>';
            $result .= '<th style="vertical-align: middle; width: 110px;"><a onclick="sortColumn(0)"><b>Код проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 400px;"><a onclick="sortColumn(1)"><b>Описание проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 220px;"><a onclick="sortColumn(2)"><b>Место возникновения</b></a></th>';
            $result .= '<th style="vertical-align: middle;"><a onclick="sortColumn(3)"><b>Отдел</b></a></th>';
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
                    $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                    $result .= '<td>' . $errorName->name . '</td>';
                    $result .= '<td>' . Html::a($group->number, \yii\helpers\Url::to(['training-group/view', 'id' => $group->id])) . '</td>';
                    $result .= '<td>' . Html::a($group->branchName, \yii\helpers\Url::to(['branch/view', 'id' => $group->branch_id])) . '</td>';
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
            if ($actual === 0)
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
        if (count($programs) === 0)
            $programs = '';

        if ($programs !== '')
        {
            $result .= '<table id="training-program" class="table table-bordered"><h4 style="text-align: center;"><u><a onclick="hide(1)">Ошибки в образовательных программах</a></u></h4>';
            $result .= '<thead>';
            $result .= '<th style="vertical-align: middle; width: 110px;"><a onclick="sortColumn(0)"><b>Код проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 400px;"><a onclick="sortColumn(1)"><b>Описание проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 220px;"><a onclick="sortColumn(2)"><b>Место возникновения</b></a></th>';
            $result .= '<th style="vertical-align: middle;"><a onclick="sortColumn(3)"><b>Отдел</b></a></th>';
            $result .= '</thead>';
            $result .= '<tbody>';

            foreach ($programs as $program)
            {
                $errorsList = ProgramErrorsWork::find()->where(['training_program_id' => $program->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
                $branchs = BranchProgramWork::find()->where(['training_program_id' => $program->id])->all();
                foreach ($errorsList as $error)
                {
                    if ($error->critical == 1)
                        $result .= '<tr style="background-color: #FCF8E3;">';
                    else
                        $result .= '<tr>';
                    $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
                    $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                    $result .= '<td>' . $errorName->name . '</td>';
                    $result .= '<td>' . Html::a($program->name, \yii\helpers\Url::to(['training-program/view', 'id' => $program->id])) . '</td>';
                    $result .= '<td>';
                    foreach ($branchs as $branch)
                        $result .= Html::a($branch->branch->name, \yii\helpers\Url::to(['branch/view', 'id' => $branch->branch_id])) . '<br>';
                    $result .= '</td>';
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
        if ($result !== '')
            $result .= '<br><br>';
        $result .= $this->ErrorsToTrainingProgram($user, $critical);
        return $result;
    }

    public function ForAdmin()
    {
        $result = '<table id="training-group" class="table table-bordered">';
        $result .= '<h4 style="text-align: center;"><u>Ошибки в системе: </u></h4>';
        $result .= '<thead>';
        $result .= '<th style="vertical-align: middle; width: 110px;"><b>Код проблемы</b></th>';
        $result .= '<th style="vertical-align: middle; width: 400px;"><b>Описание проблемы</b></th>';
        $result .= '<th style="vertical-align: middle; width: 220px;"><b>Место возникновения</b></th>';
        $result .= '<th style="vertical-align: middle;"><b>Отдел</b></th>';
        $result .= '</thead>' . '<tbody>';

        $errorsList = GroupErrorsWork::find()->where(['time_the_end' => NULL, 'amnesty' => NULL, 'critical' => 1])->orderBy(['time_start'])->all();
        foreach ($errorsList as $error)
        {
            $result .= '<tr>';
            $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
            $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
            $result .= '<td>' . $errorName->name . '</td>';
            $groupName = TrainingGroupWork::find()->where(['id' => $error->training_group_id])->one();
            $result .= '<td>' . $groupName->number . '</td>';
            $result .= '<td>' . $groupName->branchName . '</td>';
            $result .= '</tr>';
        }

        $errorsList = ProgramErrorsWork::find()->where(['time_the_end' => NULL, 'amnesty' => NULL])->orderBy(['time_start'])->all();
        foreach ($errorsList as $error)
        {
            $result .= '<tr>';
            $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
            $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
            $result .= '<td>' . $errorName->name . '</td>';
            $programName = TrainingProgramWork::find()->where(['id' => $error->training_program_id])->one();
            $result .= '<td>' . $programName->name . '</td>';
            $result .= '<td>';
            $branchs = BranchProgramWork::find()->where(['training_program_id' => $programName->id])->all();
            foreach ($branchs as $branch)
                $result .= $branch->branch->name . '<br>';
            $result .= '</td>';
            $result .= '</tr>';
        }

        $result .= '</tbode></table>';

        return $result;
    }
}
