<?php

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\AccessLevelWork */

//$this->title = $model->people->secondname.' '.$model->responsibilityType->name;
?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <?= $this->render('menu') ?>

    <div class="content-container" style="float: left">
        <h3>Выдать токен доступа</h3>
        <br>

        <?php $form = ActiveForm::begin(); ?>

        <?php
        $users = \app\models\work\UserWork::find()->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
        $items = \yii\helpers\ArrayHelper::map($users,'id','fullName');
        $params = [
        ];
        echo $form->field($model, 'user_id')->dropDownList($items,$params)->label('Пользователь');

        ?>

        <?php
        $accesses = \app\models\work\RoleFunctionWork::find()->orderBy(['name' => SORT_ASC])->all();
        $items = \yii\helpers\ArrayHelper::map($accesses,'id','name');
        $params = [
        ];
        echo $form->field($model, 'role_function_id')->dropDownList($items,$params)->label('Разрешение');

        ?>
        <div class="col-xs-12" style="float: left; padding-left: 0">
            <div class="col-xs-3" style="padding-left: 0">
                <h4>Время жизни токена</h4>
            </div>

            <div class="col-xs-3">
                <?= $form->field($model, 'weeks')->textInput(['type' => 'number', 'style' => 'max-width: 70px', 'value' => 0])->label('Недели') ?>
            </div>

            <div class="col-xs-3">
                <?= $form->field($model, 'days')->textInput(['type' => 'number', 'style' => 'max-width: 70px', 'value' => 0])->label('Дни') ?>
            </div>

            <div class="col-xs-3">
                <?= $form->field($model, 'hours')->textInput(['type' => 'number', 'style' => 'max-width: 70px', 'value' => 0])->label('Часы') ?>
            </div>

        </div>
        <div class="panel-body" style="padding: 0; margin: 0"></div>



        <div class="form-group">
            <?= Html::submitButton('Выдать токен', ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>
    </div>
    <div class="panel-body" style="padding: 0; margin: 0"></div>

        <br>
        <h3>Активные токены</h3>
        <?php

        $levels = \app\models\work\AccessLevelWork::find()->orderBy(['start_time' => SORT_DESC])->all();

        ?>
        <table class="table table-striped">
            <?php

            foreach ($levels as $level)
            {
                echo '<tr>';
                echo '<td>'.$level->userWork->fullName.'</td><td>'.$level->roleFunctionWork->name.'</td><td>'.date('d.m.Y (H:i)', strtotime($level->start_time)).'</td><td>'.date('d.m.Y (H:i)', strtotime($level->end_time)).'</td>'.
                '<td>'.Html::a('Отозвать токен', \yii\helpers\Url::to(['lk/delete-token', 'id' => $level->id]), ['class' => 'btn btn-danger']).'</td>';
                echo '</tr>';
            }

            ?>
        </table>

</div>
<div style="width:100%; height:1px; clear:both;"></div>