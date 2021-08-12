<?php

use app\models\work\ErrorsWork;
use app\models\work\GroupErrorsWork;
use app\models\work\PeopleWork;
use app\models\work\TeacherGroupWork;
use app\models\work\TrainingGroupWork;
use app\models\work\UserWork;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\LocalResponsibilityWork */

//$this->title = $model->people->secondname.' '.$model->responsibilityType->name;
?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <?= $this->render('menu') ?>

    <div class="content-container col-xs-8" style="float: left">
        <table class="table table-bordered">
            <?php
                $user = UserWork::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
                $groups = TeacherGroupWork::find()->where(['teacher_id' => $user->aka])->all();

                echo '<thead>';
                echo '<th style="vertical-align: middle;">Код проблемы</th>';
                echo '<th style="vertical-align: middle;">Описание проблемы</th>';
                echo '<th style="vertical-align: middle;">Место возникновения</th>';
                echo '</thead>';

                echo '<tbody>';
                foreach ($groups as $group)
                {
                    $trainingGroup = TrainingGroupWork::find()->where(['id' => $group->training_group_id])->one();
                    $errorsList = GroupErrorsWork::find()->where(['training_group_id' => $trainingGroup->id])->all();

                    foreach ($errorsList as $error)
                    {
                        if ($error->time_the_end == NULL)
                        {
                            echo '<tr>';
                            $errorName = ErrorsWork::find()->where(['id' => $error->errors_id])->one();
                            echo '<th style="text-align: left;">' . $errorName->number . "</th>";
                            echo '<td>' . $errorName->name . '</td>';
                            echo '<td>' . Html::a($trainingGroup->number, \yii\helpers\Url::to(['training-group/view', 'id' => $trainingGroup->id])) . '</td>';
                            echo '</tr>';
                        }
                    }
                }
                echo '</tbode>';
            ?>
        </table>
    </div>
</div>
<div style="width:100%; height:1px; clear:both;"></div>