<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;
use yii\jui\AutoComplete;
use app\models\work\PositionWork;

/* @var $this yii\web\View */
/* @var $model app\models\work\PeopleWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="people-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'secondname')->textInput(['maxlength' => true])->label('Фамилия') ?>

    <?= $form->field($model, 'firstname')->textInput(['maxlength' => true])->label('Имя') ?>

    <?= $form->field($model, 'patronymic')->textInput(['maxlength' => true])->label('Отчество') ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-briefcase"></i> Должности</h4></div>
            <?php
            $positions = \app\models\work\PeoplePositionBranchWork::find()->where(['people_id' => $model->id])->all();
            if ($positions != null)
            {
                echo '<table class="table table-bordered">';
                echo '<tr><td><b>Должность</b></td><td><b>Отдел</b></td><td></td></tr>';
                foreach ($positions  as $position) {
                    echo '<tr><td>'.$position->position->name.'</td><td>'.$position->branch->name.'</td><td>'.Html::a('Удалить', \yii\helpers\Url::to(['people/delete-position', 'id' => $position->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                }
                echo '</table>';
            }
            ?>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper1', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items1', // required: css class selector
                    'widgetItem' => '.item1', // required: css class
                    'limit' => 10, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelPeoplePositionBranch[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'eventExternalName',
                    ],
                ]); ?>

                <div class="container-items1" ><!-- widgetContainer -->
                    <?php foreach ($modelPeoplePositionBranch as $i => $modelPeoplePositionBranchOne): ?>
                        <div class="item1 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Должность</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php
                                // necessary for update action.
                                if (! $modelPeoplePositionBranchOne->isNewRecord) {
                                    echo Html::activeHiddenInput($modelPeoplePositionBranchOne, "[{$i}]id");
                                }
                                ?>
                                <div class="col-xs-4">
                                    <?php

                                    $pos = \app\models\work\PositionWork::find()->orderBy('name ASC')->all();
                                    $items = \yii\helpers\ArrayHelper::map($pos,'id','name');
                                    $params = [
                                        'prompt' => '',
                                    ];
                                    echo $form->field($modelPeoplePositionBranchOne, "[{$i}]position_id")->dropDownList($items,$params)->label('Должность');
                                    ?>

                                </div>
                                <div class="col-xs-4">
                                    <?php

                                    $branch = \app\models\work\BranchWork::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($branch,'id','name');
                                    $params = [
                                        'prompt' => '',
                                    ];
                                    echo $form->field($modelPeoplePositionBranchOne, "[{$i}]branch_id")->dropDownList($items,$params)->label('Отдел (при наличии)');
                                    ?>

                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <?php

    /*$positionList = Position::find()->select(['name as value', 'name as label'])->asArray()->all();
    echo $form->field($model, 'stringPosition')->widget(AutoComplete::className(), [
                                            'clientOptions' => [
                                                'source' => $positionList,
                                            ],
                                            'options' => [
                                                'class' => 'form-control',
                                            ]
                                        ])->label('Должность');
    //$position = \app\models\work\PositionWork::find()->all();
    //$items = \yii\helpers\ArrayHelper::map($position,'id','name');
    //$params = [];
    //echo $form->field($model, 'position_id')->dropDownList($items,$params)->label('Должность');
    */
    ?>

    <?php
    $company = \app\models\work\CompanyWork::find()->orderBy('name')->all();
    $items = \yii\helpers\ArrayHelper::map($company,'id','name');
    $params = [
        'id' => 'org'
    ];
    echo $form->field($model, 'company_id')->dropDownList($items,$params)->label('Организация');

    ?>

    <?php
    if ($model->company_id == 8)
    {
        echo '<div id="orghid">';
    }
    else
    {
        echo '<div id="orghid" hidden>';
    }
    ?>
        <?= $form->field($model, 'short')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'genitive')->textInput(['maxlength' => true])->label('Фамилия в обороте "назначить <i>кого</i>"') ?>
        <?php
        $branchs = \app\models\work\BranchWork::find()->all();
        $items = \yii\helpers\ArrayHelper::map($branchs,'id','name');
        $params = [
            'prompt' => '',
        ];
        echo $form->field($model, 'branch_id')->dropDownList($items,$params);

        ?>

        <?= $form->field($model, 'birthdate')->widget(DatePicker::class, [
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
                'yearRange' => '1940:2050',
                //'showOn' => 'button',
                //'buttonText' => 'Выбрать дату',
                //'buttonImageOnly' => true,
                //'buttonImage' => 'images/calendar.gif'
            ]]) ?>

        <?= $form->field($model, 'sex')->radioList(array(0 => 'Мужской',
            1 => 'Женский', 2 => 'Другое'), ['value' => $model->sex, 'class' => 'i-checks']) ?>
    </div>


    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

<script>
    $("#org").change(function() {
        if (this.options[this.selectedIndex].value === '8')
            $("#orghid").removeAttr("hidden");
        else
            $("#orghid").attr("hidden", "true");
    });
</script>