<?php

use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\TrainingProgram */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="training-program-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ped_council_date')->widget(DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        //'dateFormat' => 'dd.MM.yyyy,
        'options' => [
            'placeholder' => 'Дата',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '1980:2050',
            //'showOn' => 'button',
            //'buttonText' => 'Выбрать дату',
            //'buttonImageOnly' => true,
            //'buttonImage' => 'images/calendar.gif'
        ]]) ?>

    <?= $form->field($model, 'ped_council_number')->textInput(['maxlength' => true]) ?>

    <?php
    $people = \app\models\common\People::find()->where(['company_id' => 8])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
    ];
    echo $form->field($model, "author_id")->dropDownList($items,$params);
    ?>

    <?= $form->field($model, 'capacity')->textInput() ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Возраст учащихся</h4></div>
            <div class="panel-body">
                <?= $form->field($model, 'student_left_age')->textInput(['value' => $model->student_left_age == null ? 5 : $model->student_left_age]) ?>
                <?= $form->field($model, 'student_right_age')->textInput(['value' => $model->student_right_age == null ? 18 : $model->student_right_age]) ?>
            </div>
        </div>
    </div>

    <?php
    $focus = \app\models\common\Focus::find()->all();
    $items = \yii\helpers\ArrayHelper::map($focus,'id','name');
    $params = [
    ];
    echo $form->field($model, "focus_id")->dropDownList($items,$params);
    ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Отдел(-ы) - место реализации</h4></div>
            <div class="panel-body">

                <?php
                $tech = \app\models\common\BranchProgram::find()->where(['branch_id' => 2])->andWhere(['training_program_id' => $model->id])->all();
                $quant = \app\models\common\BranchProgram::find()->where(['branch_id' => 1])->andWhere(['training_program_id' => $model->id])->all();
                $cdntt = \app\models\common\BranchProgram::find()->where(['branch_id' => 3])->andWhere(['training_program_id' => $model->id])->all();
                $value = 'false';
                ?>
                <?php if (count($tech) > 0) $value = true; else $value = false; ?>
                <?= $form->field($model, 'isTechnopark')->checkbox(['checked' => $value]) ?>

                <?php if (count($quant) > 0) $value = true; else $value = false; ?>
                <?= $form->field($model, 'isQuantorium')->checkbox(['checked' => $value]) ?>

                <?php if (count($cdntt) > 0) $value = true; else $value = false; ?>
                <?= $form->field($model, 'isCDNTT')->checkbox(['checked' => $value]) ?>
            </div>
        </div>
    </div>

    <?php
    $prog = \app\models\common\TrainingProgram::find()->where(['id' => $model->id])->one();
    $value = false;
    if ($prog !== null)
    {
        $value = $prog->allow_remote == 0 ? $value = false : $value = true;
    }
    echo $form->field($model, 'allow_remote')->checkbox(['checked' => $value]);
    ?>

    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'docFile')->fileInput() ?>
    <?php
    if (strlen($model->doc_file) > 2)
        echo '<h5>Загруженный файл: '.Html::a($model->doc_file, \yii\helpers\Url::to(['training-program/get-file', 'fileName' => $model->doc_file, 'type' => 'doc'])).'&nbsp;&nbsp;&nbsp;&nbsp; '.Html::a('X', \yii\helpers\Url::to(['training-program/delete-file', 'fileName' => $model->doc_file, 'modelId' => $model->id, 'type' => 'doc'])).'</h5><br>';
    ?>

    <?= $form->field($model, 'editDocs[]')->fileInput(['multiple' => true]) ?>
    <?php
    if ($model->edit_docs !== null)
    {
        $split = explode(" ", $model->edit_docs);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['training-program/get-file', 'fileName' => $split[$i]])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['training-program/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id])).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
