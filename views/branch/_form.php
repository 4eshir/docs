<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\BranchWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="branch-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Список помещений</h4></div>
            <div>
                <?php
                $auds = \app\models\work\AuditoriumWork::find()->where(['branch_id' => $model->id])->all();
                if ($auds != null)
                {
                    echo '<table class="table table-bordered">';
                    echo '<tr><td><b>Номер</b></td><td><b>Площадь (кв.м.)</b></td><td><b>Предназначено для обр. деят.</b></td><td></td></tr>';
                    foreach ($auds as $aud) {
                        echo '<tr><td><h5>'.$aud->name.'</h5></td><td>'.$aud->square.'</td><td>'.$aud->isEducation.'</td><td>'.Html::a('Удалить', \yii\helpers\Url::to(['branch/delete-auditorium', 'id' => $aud->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                    }
                    echo '</table>';
                }
                ?>
            </div>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items4', // required: css class selector
                    'widgetItem' => '.item4', // required: css class
                    'limit' => 100, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item4', // css class
                    'deleteButton' => '.remove-item4', // css class
                    'model' => $modelAuditorium[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'eventExternalName',
                    ],
                ]); ?>

                <div class="container-items4" ><!-- widgetContainer -->
                    <?php foreach ($modelAuditorium as $i => $modelAuditoriumOne): ?>
                        <div class="item4 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Помещение</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item4 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item4 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php
                                // necessary for update action.
                                if (! $modelAuditoriumOne->isNewRecord) {
                                    echo Html::activeHiddenInput($modelAuditoriumOne, "[{$i}]id");
                                }
                                ?>
                                <div class="col-xs-4">
                                    <?= $form->field($modelAuditoriumOne, "[{$i}]name")->textInput(); ?>
                                </div>
                                <div class="col-xs-4">
                                    <?= $form->field($modelAuditoriumOne, "[{$i}]square")->textInput(); ?>
                                </div>
                                <div class="col-xs-4">
                                    <?= $form->field($modelAuditoriumOne, "[{$i}]is_education")->checkbox(); ?>
                                </div>


                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
