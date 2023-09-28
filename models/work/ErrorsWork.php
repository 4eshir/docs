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
            $branch = PeopleWork::find()->where(['id' => $user->aka])->one()->branchWork->id;
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
            $branch = PeopleWork::find()->where(['id' => $user->aka])->one()->branch_id;
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
                    if (count($branchs) !== 0)
                        foreach ($branchs as $branch)
                            $result .= Html::a($branch->branch->name, \yii\helpers\Url::to(['branch/view', 'id' => $branch->branch_id])) . '<br>';
                    else
                        $result .= '<p style="color: red;">не указан</p>';
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
        if ($result !== '')
            $result .= '<br><br>';
        $result .= $this->ErrorsToEvent($user);
        if ($result !== '')
            $result .= '<br><br>';
        $result .= $this->ErrorsToForeignEvent($user);
        if ($result !== '')
            $result .= '<br><br>';
        $result .= $this->ErrorsToContract($user);
        if ($result !== '')
            $result .= '<br><br>';
        $result .= $this->ErrorsToInvoice($user);
        if ($result !== '')
            $result .= '<br><br>';
        $result .= $this->ErrorsToMaterialObject($user);
        if ($result !== '')
            $result .= '<br><br>';
        $result .= $this->ErrorsToContainer($user);
        return $result;
    }

    public function SystemCriticalMessage($user, $functions)
    {
        $result = '';
        $groups = '';
        $programs = '';
        $orders = '';
        $events = '';
        $foreignEvents = '';
        $groupsSet = TrainingGroupWork::find();
        $programsSet = TrainingProgramWork::find();
        $ordersSet = DocumentOrderWork::find();
        $eventsSet = EventWork::find();
        $foreignEventsSet = ForeignEventWork::find();

        $branch = PeopleWork::find()->where(['id' => $user->aka])->one()->branch->id;

        // Образовательные программы
        if (count(array_intersect([16], $functions)) > 0)
            $programs = $programsSet->where(['actual' => 1])->all();
        else if (count(array_intersect([15], $functions)) > 0)
            $programs = $programsSet->joinWith(['branchPrograms branchPrograms'])->where(['branchPrograms.branch_id' => $branch])->andWhere(['actual' => 1])->all();

        // Учебные группы
        if (count(array_intersect([14], $functions)) > 0)
            $groups = $groupsSet->all();
        else if (count(array_intersect([13], $functions)) > 0)
            $groups = $groupsSet->where(['branch_id' => $branch])->all();
        else if (count(array_intersect([12], $functions)) > 0)
            $groups = $groupsSet->joinWith(['teacherGroups teacherGroups'])->where(['teacherGroups.teacher_id' => $user->aka])->all();

        // Приказы
        if (count(array_intersect([32], $functions)) > 0 && count(array_intersect([24], $functions)) > 0)
            $orders = $ordersSet->all();
        else if (count(array_intersect([32], $functions)) > 0)
            $orders = $ordersSet->where(['type' => 1])->orWhere(['type' => 10])->all();
        else if (count(array_intersect([24], $functions)) > 0)
            $orders = $ordersSet->where(['nomenclature_id' => $branch])->andWhere(['IN', 'id',
                (new Query())->select('id')->from('document_order')->where(['type' => 0])->orWhere(['type' => 11])])->all();

        // Мероприятия
        if (count(array_intersect([38], $functions)) > 0)
            $events = $eventsSet->all();

        // Участие в мероприятиях
        if (count(array_intersect([40], $functions)) > 0)
            $foreignEvents = $foreignEventsSet->all();


        if ($groups !== '' || $programs !== '' || $orders !== '' || $events !== '' || $foreignEvents !== '')
        {
            $result .= '<table id="training-group" class="table table-bordered">';
            $result .= '<h4 style="text-align: center;"><u>Ошибки ЦСХД связанные с некорректно заполненными данными</u></h4>';
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

            if ($orders !== '')
            {
                $errorsListSet = OrderErrorsWork::find();
                $branchsSet = BranchWork::find();
                foreach ($orders as $order)
                {
                    $errorsList = $errorsListSet->where(['document_order_id' => $order->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
                    $branchs = $branchsSet->where(['id' => $order->nomenclature_id])->one();
                    foreach ($errorsList as $error)
                    {
                        $result .= '<tr>';
                        $errorName = $errorNameSet->where(['id' => $error->errors_id])->one();
                        $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                        $result .= '<td>' . $errorName->name . '</td>';
                        $result .= '<td>' . $order->order_name . '</td>';
                        $result .= '<td>' . $branchs->name . '</td>';
                        $result .= '</tr>';
                    }
                }
            }

            if ($events != '')
            {
                $errorsListSet = EventErrorsWork::find();
                $branchsSet = EventBranchWork::find();

                foreach ($events as $event)
                {
                    $errorsList = $errorsListSet->where(['event_id' => $event->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
                    $branchs = $branchsSet->where(['event_id' => $event->id])->all();
                    foreach ($errorsList as $error)
                    {
                        $result .= '<tr>';
                        $errorName = $errorNameSet->where(['id' => $error->errors_id])->one();
                        $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                        $result .= '<td>' . $errorName->name . '</td>';
                        $result .= '<td>' . $event->name . '</td>';
                        $result .= '<td>';
                        foreach ($branchs as $branch)
                            $result .= $branch->branch->name . '<br>';
                        $result .= '</td></tr>';
                    }
                }
            }

            if ($foreignEvents != '')
            {
                $errorsListSet = ForeignEventErrorsWork::find();
                $branchsSet = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant']);

                foreach ($foreignEvents as $foreignEvent)
                {
                    $errorsList = $errorsListSet->where(['foreign_event_id' => $foreignEvent->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
                    $branchsEvent = $branchsSet->where(['teacherParticipant.foreign_event_id' => $foreignEvent->id])->all();
                    $branchsNoDouble = [];
                    foreach ($branchsEvent as $branch)
                        $branchsNoDouble[] = $branch->branch_id;

                    if (count($branchsNoDouble) > 1)
                        $branchsNoDouble = array_unique($branchsNoDouble);

                    foreach ($errorsList as $error)
                    {
                        $result .= '<tr>';
                        $errorName = $errorNameSet->where(['id' => $error->errors_id])->one();
                        $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                        $result .= '<td>' . $errorName->name . '</td>';
                        $result .= '<td>' . $foreignEvent->name . '</td>';
                        $result .= '<td>';
                        if (count($branchsNoDouble) !== 0)
                            foreach ($branchsNoDouble as $branch)
                                $result .= $branch->branch->name . '<br>';
                        else
                            $result .= '<p style="color: red;">не указан</p>';
                        $result .= '</td></tr>';
                    }
                }
            }

            $result .= '</tbode></table>';
        }

        return $result;
    }

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
            $branch = PeopleWork::find()->where(['id' => $user->aka])->one()->branch_id;
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
                    $result .= '<td>' . Html::a('Приказ № '.$document->getDocumentNumberString().' '.$document->order_name, \yii\helpers\Url::to(['document-order/view', 'id' => $document->id])) . '</td>';
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

    private function ErrorsToEvent($user)
    {
        $result = '';
        $events = '';
        if (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 3)) // значит информатор по мероприятиям
        {
            $branch = PeopleWork::find()->where(['id' => $user->aka])->one()->branch->id;
            $events = EventWork::find()->where(['IN', 'id',
                            (new Query())->select('id')->from('event_branch')->where(['branch_id' => $branch])])->all();
        }
        else if (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7) ||
                \app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 6))   // значит админ или суперконтролер
        {
            $events = EventWork::find()->all();
        }

        if ($events !== '')
        {
            $result .= '<table id="event" style="display: block" class="table table-bordered"><h4 style="text-align: center;"><u><a onclick="hide(3)">Ошибки в мероприятиях</a></u></h4>';
            $result .= '<thead>';
            $result .= '<th style="vertical-align: middle; width: 110px;"><a onclick="sortColumn(0)"><b>Код проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 400px;"><a onclick="sortColumn(1)"><b>Описание проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 220px;"><a onclick="sortColumn(2)"><b>Место возникновения</b></a></th>';
            $result .= '<th style="vertical-align: middle;"><a onclick="sortColumn(3)"><b>Отдел</b></a></th>';
            $result .= '</thead>';
            $result .= '<tbody>';
            foreach ($events as $event)
            {
                $errorsList = EventErrorsWork::find()->where(['event_id' => $event->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
                $branchsEvent = EventBranchWork::find()->where(['event_id' => $event->id])->all();
                $branchsName = BranchWork::find();
                foreach ($errorsList as $error)
                {
                    if ($error->critical == 1)
                        $result .= '<tr style="background-color: #FCF8E3;">';
                    else
                        $result .= '<tr>';
                    $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
                    $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                    $result .= '<td>' . $errorName->name . '</td>';
                    $result .= '<td>' . Html::a($event->name, \yii\helpers\Url::to(['event/view', 'id' => $event->id])) . '</td>';
                    $result .= '<td>';
                    if (count($branchsEvent) !== 0)
                        foreach ($branchsEvent as $branchName)
                        {
                            $result .= Html::a($branchsName->where(['id' => $branchName->branch_id])->one()->name, \yii\helpers\Url::to(['branch/view', 'id' => $branchName->branch_id])) . '<br>';
                        }
                    else
                        $result .= '<p style="color: red;">не указан</p>';
                    $result .= '</td>';
                    $result .= '</tr>';
                }
            }

            $result .= '</tbody></table>';
        }
        return $result;
    }

    private function ErrorsToForeignEvent($user)
    {
        $result = '';
        $foreignEvents = '';
        if (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 3)) // значит информатор по мероприятиям
        {
            $branch = PeopleWork::find()->where(['id' => $user->aka])->one()->branch->id;
            $foreignEvents = ForeignEventWork::find()->where(['IN', 'id',
                (new Query())->select('id')->from('teacher_participant')->where(['branch_id' => $branch])])->all();
        }
        else if (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7) ||
                \app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 6))   // значит админ или суперконтролер
        {
            $foreignEvents = ForeignEventWork::find()->all();
        }

        if ($foreignEvents !== '')
        {
            $result .= '<table id="foreignEvent" style="display: block" class="table table-bordered"><h4 style="text-align: center;"><u><a onclick="hide(4)">Ошибки в учете достижений</a></u></h4>';
            $result .= '<thead>';
            $result .= '<th style="vertical-align: middle; width: 110px;"><a onclick="sortColumn(0)"><b>Код проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 400px;"><a onclick="sortColumn(1)"><b>Описание проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 220px;"><a onclick="sortColumn(2)"><b>Место возникновения</b></a></th>';
            $result .= '<th style="vertical-align: middle;"><a onclick="sortColumn(3)"><b>Отдел</b></a></th>';
            $result .= '</thead>';
            $result .= '<tbody>';

            foreach ($foreignEvents as $foreignEvent)
            {
                $errorsList = ForeignEventErrorsWork::find()->where(['foreign_event_id' => $foreignEvent->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
                $branchsEvent = TeacherParticipantBranchWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.foreign_event_id' => $foreignEvent->id])->all();
                $branchsName = BranchWork::find();
                $branchsSet = [];
                foreach ($branchsEvent as $branch)
                {
                    $branchsSet[] = $branch->branch_id;
                }
                if (count($branchsSet) > 1)
                    $branchsSet = array_unique($branchsSet);

                foreach ($errorsList as $error)
                {
                    if ($error->critical == 1)
                        $result .= '<tr style="background-color: #FCF8E3;">';
                    else
                        $result .= '<tr>';
                    $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
                    $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                    $result .= '<td>' . $errorName->name . '</td>';
                    $result .= '<td>' . Html::a($foreignEvent->name, \yii\helpers\Url::to(['foreign-event/view', 'id' => $foreignEvent->id])) . '</td>';
                    $result .= '<td>';
                    if (count($branchsSet) !== 0)
                        foreach ($branchsSet as $branchID)
                        {
                            $result .= Html::a($branchsName->where(['id' => $branchID])->one()->name, \yii\helpers\Url::to(['branch/view', 'id' => $branchID])) . '<br>';
                        }
                    else
                        $result .= '<p style="color: red;">не указан</p>';
                    $result .= '</td>';
                    $result .= '</tr>';
                }
            }

            $result .= '</tbody></table>';
        }
        return $result;
    }

    private function ErrorsToContract($user)
    {
        $result = '';

        $contracts = '';
        if (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7) ||
            \app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 8))   // значит админ или информатор по мат объектам
        {
            $contracts = ContractWork::find()->all();
        }

        if ($contracts !== '')
        {
            $result .= '<table id="contract" style="display: block" class="table table-bordered"><h4 style="text-align: center;"><u><a onclick="hide(5)">Ошибки в договорах</a></u></h4>';
            $result .= '<thead>';
            $result .= '<th style="vertical-align: middle; width: 110px;"><a onclick="sortColumn(0)"><b>Код проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 400px;"><a onclick="sortColumn(1)"><b>Описание проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 220px;"><a onclick="sortColumn(2)"><b>Место возникновения</b></a></th>';
            $result .= '<th style="vertical-align: middle;"><a onclick="sortColumn(3)"><b>Отдел</b></a></th>';
            $result .= '</thead>';
            $result .= '<tbody>';

            foreach ($contracts as $contract)
            {
                $errorsList = ContractErrorsWork::find()->where(['contract_id' => $contract->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();

                foreach ($errorsList as $error)
                {
                    if ($error->critical == 1)
                        $result .= '<tr style="background-color: #FCF8E3;">';
                    else
                        $result .= '<tr>';
                    $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
                    $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                    $result .= '<td>' . $errorName->name . '</td>';
                    $result .= '<td>' . Html::a($contract->getContractFullName(), \yii\helpers\Url::to(['contract/view', 'id' => $contract->id])) . '</td>';
                    $result .= '<td>';
                    $result .= '<p style="color: red;">не указан</p>';
                    $result .= '</td>';
                    $result .= '</tr>';
                }
            }

            $result .= '</tbody></table>';
        }
        return $result;
    }

    private function ErrorsToInvoice($user)
    {
        $result = '';

        $invoices = '';
        if (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7) ||
            \app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 8))   // значит админ или информатор по мат объектам
        {
            $invoices = InvoiceWork::find()->all();
        }

        if ($invoices !== '')
        {
            $result .= '<table id="invoice" style="display: block" class="table table-bordered"><h4 style="text-align: center;"><u><a onclick="hide(6)">Ошибки в документах о поступлении материальных ценностей</a></u></h4>';
            $result .= '<thead>';
            $result .= '<th style="vertical-align: middle; width: 110px;"><a onclick="sortColumn(0)"><b>Код проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 400px;"><a onclick="sortColumn(1)"><b>Описание проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 220px;"><a onclick="sortColumn(2)"><b>Место возникновения</b></a></th>';
            $result .= '<th style="vertical-align: middle;"><a onclick="sortColumn(3)"><b>Отдел</b></a></th>';
            $result .= '</thead>';
            $result .= '<tbody>';

            foreach ($invoices as $invoice)
            {
                $errorsList = InvoiceErrorsWork::find()->where(['invoice_id' => $invoice->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();

                foreach ($errorsList as $error)
                {
                    if ($error->critical == 1)
                        $result .= '<tr style="background-color: #FCF8E3;">';
                    else
                        $result .= '<tr>';
                    $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
                    $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                    $result .= '<td>' . $errorName->name . '</td>';
                    $result .= '<td>' . Html::a($invoice->getNumberString(), \yii\helpers\Url::to(['contract/view', 'id' => $invoice->id])) . '</td>';
                    $result .= '<td>';
                    $result .= '<p style="color: red;">не указан</p>';
                    $result .= '</td>';
                    $result .= '</tr>';
                }
            }

            $result .= '</tbody></table>';
        }
        return $result;
    }

    private function ErrorsToMaterialObject($user)
    {
        $result = '';

        $materialObjects = '';
        if (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7) ||
            \app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 8))   // значит админ или информатор по мат объектам
        {
            $materialObjects = MaterialObjectWork::find()->all();
        }

        if ($materialObjects !== '')
        {
            $result .= '<table id="materialObject" style="display: block" class="table table-bordered"><h4 style="text-align: center;"><u><a onclick="hide(7)">Ошибки в материальных ценностях</a></u></h4>';
            $result .= '<thead>';
            $result .= '<th style="vertical-align: middle; width: 110px;"><a onclick="sortColumn(0)"><b>Код проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 400px;"><a onclick="sortColumn(1)"><b>Описание проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 220px;"><a onclick="sortColumn(2)"><b>Место возникновения</b></a></th>';
            $result .= '<th style="vertical-align: middle;"><a onclick="sortColumn(3)"><b>Отдел</b></a></th>';
            $result .= '</thead>';
            $result .= '<tbody>';

            foreach ($materialObjects as $materialObject)
            {
                $errorsList = MaterialObjectErrorsWork::find()->where(['material_object_id' => $materialObject->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();

                foreach ($errorsList as $error)
                {
                    if ($error->critical == 1)
                        $result .= '<tr style="background-color: #FCF8E3;">';
                    else
                        $result .= '<tr>';
                    $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
                    $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                    $result .= '<td>' . $errorName->name . '</td>';
                    $result .= '<td>' . Html::a($materialObject->nameAndNumberMaterialObject, \yii\helpers\Url::to(['contract/view', 'id' => $materialObject->id])) . '</td>';
                    $result .= '<td>';
                    $result .= '<p style="color: red;">не указан</p>';
                    $result .= '</td>';
                    $result .= '</tr>';
                }
            }

            $result .= '</tbody></table>';
        }
        return $result;
    }

    private function ErrorsToContainer($user)
    {
        $result = '';

        $containers = '';
        if (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7) ||
            \app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 8))   // значит админ или информатор по мат объектам
        {
            $containers = ContainerWork::find()->all();
        }

        if ($containers !== '')
        {
            $result .= '<table id="container" style="display: block" class="table table-bordered"><h4 style="text-align: center;"><u><a onclick="hide(8)">Ошибки в контейнерах</a></u></h4>';
            $result .= '<thead>';
            $result .= '<th style="vertical-align: middle; width: 110px;"><a onclick="sortColumn(0)"><b>Код проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 400px;"><a onclick="sortColumn(1)"><b>Описание проблемы</b></a></th>';
            $result .= '<th style="vertical-align: middle; width: 220px;"><a onclick="sortColumn(2)"><b>Место возникновения</b></a></th>';
            $result .= '<th style="vertical-align: middle;"><a onclick="sortColumn(3)"><b>Отдел</b></a></th>';
            $result .= '</thead>';
            $result .= '<tbody>';

            foreach ($containers as $container)
            {
                $errorsList = ContainerErrorsWork::find()->where(['container_id' => $container->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();

                foreach ($errorsList as $error)
                {
                    if ($error->critical == 1)
                        $result .= '<tr style="background-color: #FCF8E3;">';
                    else
                        $result .= '<tr>';
                    $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
                    $result .= '<td style="text-align: left;">' . $errorName->number . "</td>";
                    $result .= '<td>' . $errorName->name . '</td>';
                    $result .= '<td>' . Html::a($container->name, \yii\helpers\Url::to(['contract/view', 'id' => $container->id])) . '</td>';
                    $result .= '<td>';
                    $result .= '<p style="color: red;">не указан</p>';
                    $result .= '</td>';
                    $result .= '</tr>';
                }
            }

            $result .= '</tbody></table>';
        }
        return $result;
    }
}
