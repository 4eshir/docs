<?php

namespace app\models\work;

use app\models\common\Errors;
use app\models\extended\AccessTrainingGroup;
use Yii;
use yii\db\Query;
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
            $result .= '<table id="training-group" class="table table-bordered" style="display: block">';
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
        if ($programs == null || count($programs) === 0)
            $programs = '';

        if ($programs !== '')
        {
            $result .= '<table id="training-program" style="display: block" class="table table-bordered"><h4 style="text-align: center;"><u><a onclick="hide(1)">Ошибки в образовательных программах</a></u></h4>';
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

    public function ErrorsSystem($user, $critical)
    {
        $result = $this->ErrorsToGroupAndJournal($user, $critical);
        if ($result !== '')
           $result .= '<br><br>';
        $result .= $this->ErrorsToTrainingProgram($user, $critical);
        if ($result !== '')
            $result .= '<br><br>';
        $result .= $this->ErrorsToDocumentOrder($user);
        return $result;
    }

    public function EducationalCriticalMessage($user, $functions)
    {
        $result = '';
        $groups = '';
        $programs = '';
        $groupsSet = TrainingGroupWork::find();
        $programsSet = TrainingProgramWork::find();

        foreach ($functions as $function)
        {
            if ($function == 12)
                $groups = $groupsSet->joinWith(['teacherGroups teacherGroups'])->where(['teacherGroups.teacher_id' => $user->aka])->all();
            else if ($function == 15 || $function == 13)
            {
                $branch = PeopleWork::find()->where(['id' => $user->aka])->one()->branch->id;
                if ($function == 13)
                    $groups = $groupsSet->where(['branch_id' => $branch])->all();
                else
                    $programs = $programsSet->joinWith(['branchPrograms branchPrograms'])->where(['branchPrograms.branch_id' => $branch])->andWhere(['actual' => 1])->all();
            }
            else if ($function == 14)
                $groups = $groupsSet->all();
            else if ($function == 16)
                $programs = $programsSet->where(['actual' => 1])->all();
        }

        if ($groups !== '' || $programs !== '')
        {
            $result .= '<table id="training-group" class="table table-bordered">';
            $result .= '<h4 style="text-align: center;"><u>Ошибки ЦСХД связанные с процессом обучения (учебные группы, электронный журнал и образовательные программы)</u></h4>';
            $result .= '<thead>';
            $result .= '<th style="vertical-align: middle; width: 110px;"><b>Код проблемы</b></th>';
            $result .= '<th style="vertical-align: middle; width: 400px;"><b>Описание проблемы</b></th>';
            $result .= '<th style="vertical-align: middle; width: 220px;"><b>Место возникновения</b></th>';
            $result .= '<th style="vertical-align: middle;"><b>Отдел</b></th>';
            $result .= '</thead>';

            $result .= '<tbody>';

            $errorNameSet = ErrorsWork::find();

            if ($groups !== '')
            {
                $errorsListSet = GroupErrorsWork::find();
                foreach ($groups as $group)
                {
                    $errorsList = $errorsListSet->where(['training_group_id' => $group->id, 'time_the_end' => NULL, 'amnesty' => NULL, 'critical' => 1])->all();

                    foreach ($errorsList as $error)
                    {
                        $result .= '<tr>';
                        $errorName = $errorNameSet->where(['id' => $error->errors_id])->one();
                        $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                        $result .= '<td>' . $errorName->name . '</td>';
                        $result .= '<td>' . $group->number . '</td>';
                        $result .= '<td>' . $group->branchName . '</td>';
                        $result .= '</tr>';
                    }
                }
            }

            if ($programs !== '')
            {
                $errorsListSet = ProgramErrorsWork::find();
                $branchsSet = BranchProgramWork::find();
                foreach ($programs as $program)
                {
                    $errorsList = $errorsListSet->where(['training_program_id' => $program->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
                    $branchs = $branchsSet->where(['training_program_id' => $program->id])->all();
                    foreach ($errorsList as $error)
                    {
                        $result .= '<tr>';
                        $errorName = $errorNameSet->where(['id' => $error->errors_id])->one();
                        $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                        $result .= '<td>' . $errorName->name . '</td>';
                        $result .= '<td>' . $program->name . '</td>';
                        $result .= '<td>';
                        foreach ($branchs as $branch)
                            $result .= $branch->branch->name . '<br>';
                        $result .= '</td>';
                        $result .= '</tr>';
                    }
                }
            }

            $result .= '</tbode></table>';
        }

        return $result;
    }

    /*       Электронный документооборот        */

    private function ErrorsToDocumentOrder ($user)
    {
        $result = '';
        $documents = '';
        if (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 24) && \app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 32))
        {
            $documents = DocumentOrderWork::find()->all();
        }
        else if (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 24))   // образовательные
        {
            $branch = PeopleWork::find()->where(['id' => $user->aka])->one()->branch->id;
            $documents = DocumentOrderWork::find()->where(['nomenclature_id' => $branch])->andWhere(['IN', 'id',
                (new Query())->select('id')->from('document_order')->where(['type' => 0])->orWhere(['type' => 11])])->all();
        }
        else if (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 32))  // основные приказы
        {
            $documents = DocumentOrderWork::find()->where(['type' => 1])->orWhere(['type' => 10])->all();
        }

        if ($documents !== '')
        {
            $result .= '<table id="document-order" style="display: block" class="table table-bordered"><h4 style="text-align: center;"><u><a onclick="hide(2)">Ошибки в приказах (по основной и образовательной деятельности)</a></u></h4>';
            $result .= '<thead>';
            $result .= '<th style="vertical-align: middle; width: 110px;"><a onclick="sortColumn(0)"><b>Код проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 400px;"><a onclick="sortColumn(1)"><b>Описание проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 220px;"><a onclick="sortColumn(2)"><b>Место возникновения</b></a></th>';
            $result .= '<th style="vertical-align: middle;"><a onclick="sortColumn(3)"><b>Отдел</b></a></th>';
            $result .= '</thead>';
            $result .= '<tbody>';

            foreach ($documents as $document)
            {
                $errorsList = OrderErrorsWork::find()->where(['document_order_id' => $document->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
                foreach ($errorsList as $error)
                {
                    if ($error->critical == 1)
                        $result .= '<tr style="background-color: #FCF8E3;">';
                    else
                        $result .= '<tr>';
                    $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
                    $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                    $result .= '<td>' . $errorName->name . '</td>';
                    $result .= '<td>' . Html::a($document->order_name, \yii\helpers\Url::to(['document-order/view', 'id' => $document->id])) . '</td>';
                    $result .= '<td>';
                    $branchName = BranchWork::find()->where(['id' => $document->nomenclature_id])->one();
                    $result .= Html::a($branchName->name, \yii\helpers\Url::to(['branch/view', 'id' => $document->nomenclature_id])) . '<br>';
                    $result .= '</td>';
                    $result .= '</tr>';
                }
            }
            $result .= '</tbody></table>';
        }
        return $result;
    }
}
