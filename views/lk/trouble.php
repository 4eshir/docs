<?php

use app\models\work\ErrorsWork;
use app\models\work\GroupErrorsWork;
use app\models\work\PeopleWork;
use app\models\work\ProgramErrorsWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TrainingGroupWork;
use app\models\work\TrainingProgramWork;
use app\models\work\UserWork;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\work\LocalResponsibilityWork */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = $model->people->secondname.' '.$model->responsibilityType->name;
?>

<?php
$access = [12, 13, 14];
$isMethodist = \app\models\common\AccessLevel::find()->where(['user_id' => Yii::$app->user->identity->getId()])->andWhere(['in', 'access_id', $access])->one();
?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <?= $this->render('menu') ?>

    <div class="content-container col-xs-8" style="float: left">
        <table class="table table-bordered">
            <?php
                /*$user = UserWork::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
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
                    /*$teacherGroups = TeacherGroupWork::find()->joinWith(['trainingGroup trainingGroup'])->where(['teacher_group.teacher_id' => $user->aka])->andWhere(['trainingGroup.archive' => 0])->all();
                    //$trainingGroup = TrainingGroupWork::find()->where(['id' => $group->training_group_id])->one();
                    foreach ($teacherGroups as $group) {
                       $groups += TrainingGroupWork::find()->where(['id' => $group->training_group_id])->one();
                    }*/
                    //var_dump($groups);

                    //$trainingGroup = TrainingGroupWork::find()->joinWith(['teacherGroup teacherGroup'])->where(['teacherGroup.teacher_id' => $user->aka])->all();
                    //var_dump($trainingGroup);
                /*}

                //var_dump($groups);
                //var_dump(count ($groups));

                if ($groups !== '')
                {
                    echo '<h4 style="text-align: center;"><u>Ошибки в учебных группах</u></h4>';
                    echo '<thead>';
                    echo '<th style="vertical-align: middle;">Код проблемы</th>';
                    echo '<th style="vertical-align: middle;">Описание проблемы</th>';
                    echo '<th style="vertical-align: middle;">Место возникновения</th>';
                    echo '</thead>';

                    echo '<tbody>';
                    foreach ($groups as $group)
                    {
                        //$trainingGroup = TrainingGroupWork::find()->where(['id' => $group->training_group_id])->one();
                        $errorsList = GroupErrorsWork::find()->where(['training_group_id' => $group->id, 'time_the_end' => NULL, 'amnesty' => NULL])->all();
                        foreach ($errorsList as $error)
                        {
                            if ($error->critical == 1)
                                echo '<tr style="background-color: #FCF8E3;">';
                            else
                                echo '<tr>';
                            $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
                            echo '<th style="text-align: left;">' . $errorName->number . "</th>";
                            echo '<td>' . $errorName->name . '</td>';
                            echo '<td>' . Html::a($group->number, \yii\helpers\Url::to(['training-group/view', 'id' => $group->id])) . '</td>';
                            echo '</tr>';
                        }
                    }
                    echo '</tbode>';
                }





                // отображение ошибок в образовательных программах
                /*if ($user->id == 31)
                {
                    $errorsList = ProgramErrorsWork::find()->where(['time_the_end' => NULL, 'amnesty' => NULL])->all();
                    foreach ($errorsList as $error)
                    {
                        $program = TrainingProgramWork::find()->where(['id' => $error->training_program_id])->one();
                        echo '<tr>';
                        $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
                        echo '<th style="text-align: left;">' . $errorName->number . "</th>";
                        echo '<td>' . $errorName->name . '</td>';
                        echo '<td>' . Html::a($program->name, \yii\helpers\Url::to(['training-program/view', 'id' => $program->id])) . '</td>';
                        echo '</tr>';
                    }
                }*/


            ?>
        </table>
    </div>
</div>
<div style="width:100%; height:1px; clear:both;"></div>