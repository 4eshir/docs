<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\jui\DatePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\TrainingProgramWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="training-program-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php
    $themes = \app\models\work\ThematicDirectionWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($themes,'id','trueName');
    $params = [
        'prompt' => ''
    ];
    echo $form->field($model, "thematic_direction_id")->dropDownList($items,$params);

    ?>

    <?php
    $items = array(1, 2, 3);
    $params = [
    ];
    echo $form->field($model, "level")->dropDownList($items,$params);

    ?>

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

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-user"></i>Составители</h4></div>
            <?php
            $resp = \app\models\work\AuthorProgramWork::find()->where(['training_program_id' => $model->id])->all();
            if ($resp != null)
            {
                echo '<table>';
                foreach ($resp as $respOne) {
                    $respOnePeople = \app\models\work\PeopleWork::find()->where(['id' => $respOne->author_id])->one();
                    echo '<tr><td style="padding-left: 20px"><h4>'.$respOnePeople->secondname.' '.$respOnePeople->firstname.' '.$respOnePeople->patronymic.'</h4></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['training-program/delete-author', 'peopleId' => $respOnePeople->id, 'modelId' => $model->id])).'</td></tr>';
                }
                echo '</table>';
            }
            ?>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 40, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelAuthor[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'people_id',
                    ],
                ]); ?>

                <div class="container-items"><!-- widgetContainer -->
                    <?php foreach ($modelAuthor as $i => $modelAuthorOne): ?>
                        <div class="item panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading" onload="scrolling()">
                                <h3 class="panel-title pull-left">Составитель</h3>
                                <div class="pull-right">
                                    <button type="button" name="add" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body" id="scroll">

                                <?php
                                $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->all();
                                $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                $params = [
                                    'prompt' => ''
                                ];
                                echo $form->field($modelAuthorOne, "[{$i}]author_id")->dropDownList($items,$params)->label('ФИО');

                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'capacity')->textInput() ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-user"></i>Учебно-тематический план</h4></div>
            <div style="padding-left: 1.5%; padding-top: 1%">
                <?= $form->field($model, 'fileUtp')->fileInput() ?>
            </div>
            <?php
            $resp = \app\models\work\ThematicPlanWork::find()->where(['training_program_id' => $model->id])->all();
            if ($resp != null)
            {
                echo '<table class="table table-bordered">';
                echo '<tr><td><b>Тема</b></td><td><b>Форма контроля</b></td><td></td><td></td></tr>';
                foreach ($resp as $respOne) {
                    echo '<tr><td style="padding-left: 20px"><h5>'.$respOne->theme.'</h5></td><td style="padding-left: 20px"><h5>'.$respOne->controlType->name.'</h5></td>'.
                        '<td>'.Html::a('Редактировать', \yii\helpers\Url::to(['training-program/update-plan', 'id' => $respOne->id, 'modelId' => $model->id]), ['class' => 'btn btn-primary']).'</td>'.
                        '<td>'.Html::a('Удалить', \yii\helpers\Url::to(['training-program/delete-plan', 'id' => $respOne->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                }
                echo '</table>';
            }
            ?>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper4', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items4', // required: css class selector
                    'widgetItem' => '.item4', // required: css class
                    'limit' => 999, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item4', // css class
                    'deleteButton' => '.remove-item4', // css class
                    'model' => $modelThematicPlan[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'people_id',
                    ],
                ]); ?>

                <div class="container-items4"><!-- widgetContainer -->
                    <?php foreach ($modelThematicPlan as $i => $modelThematicPlanOne): ?>
                        <div class="item4 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading" onload="scrolling()">
                                <h3 class="panel-title pull-left">Тема</h3>
                                <div class="pull-right">
                                    <button type="button" name="add" class="add-item4 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item4 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body" id="scroll">
                                <div class="col-xs-4">
                                    <?= $form->field($modelThematicPlanOne, "[{$i}]theme")->textInput()->label('Тема') ?>
                                </div>
                                <div class="col-xs-4">
                                    <?php
                                    $ct = \app\models\work\ControlTypeWork::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($ct,'id','name');
                                    $params = [
                                        'prompt' => ''
                                    ];
                                    echo $form->field($modelThematicPlanOne, "[{$i}]control_type_id")->dropDownList($items,$params)->label('Форма контроля');

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
    $focus = \app\models\work\FocusWork::find()->all();
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
                $tech = \app\models\work\BranchProgramWork::find()->where(['branch_id' => 2])->andWhere(['training_program_id' => $model->id])->all();
                $quant = \app\models\work\BranchProgramWork::find()->where(['branch_id' => 1])->andWhere(['training_program_id' => $model->id])->all();
                $cdntt = \app\models\work\BranchProgramWork::find()->where(['branch_id' => 3])->andWhere(['training_program_id' => $model->id])->all();
                $mobquant = \app\models\work\BranchProgramWork::find()->where(['branch_id' => 4])->andWhere(['training_program_id' => $model->id])->all();
                $cod = \app\models\work\BranchProgramWork::find()->where(['branch_id' => 7])->andWhere(['training_program_id' => $model->id])->all();
                $value = 'false';
                ?>
                <?php if (count($tech) > 0) $value = true; else $value = false; ?>
                <?= $form->field($model, 'isTechnopark')->checkbox(['checked' => $value]) ?>

                <?php if (count($quant) > 0) $value = true; else $value = false; ?>
                <?= $form->field($model, 'isQuantorium')->checkbox(['checked' => $value]) ?>

                <?php if (count($cdntt) > 0) $value = true; else $value = false; ?>
                <?= $form->field($model, 'isCDNTT')->checkbox(['checked' => $value]) ?>

                <?php if (count($mobquant) > 0) $value = true; else $value = false; ?>
                <?= $form->field($model, 'isMobQuant')->checkbox(['checked' => $value]) ?>

                <?php if (count($cod) > 0) $value = true; else $value = false; ?>
                <?= $form->field($model, 'isCod')->checkbox(['checked' => $value]) ?>
            </div>
        </div>
    </div>

    <?php
    /*$prog = \app\models\work\TrainingProgramWork::find()->where(['id' => $model->id])->one();
    $value = false;
    if ($prog !== null)
    {
        $value = $prog->allow_remote == 0 ? $value = false : $value = true;
    }
    echo $form->field($model, 'allow_remote')->radioList([
            0 => 'Только очная форма',
            1 => 'Очная форма, с применением дистанционных технологий',
        ],
        [
            'item' => function($index, $label, $name, $checked, $value) {
                if ($checked == true)
                    $checkedStr = 'checked=""';
                else
                    $checkedStr = '';
                $return = '<label class="modal-radio">';
                $return .= '<input type="radio" name="' . $name . '" value="' . $value . '" tabindex="3" style="margin-right: 5px" '.$checkedStr.'>';
                $return .= '<i></i>';
                $return .= '<span>' . ucwords($label) . '</span>';
                $return .= '</label><br>';

                return $return;
            }
        ])->label('Форма реализации');
    */
    ?>

    <?php
    $allowRemote = \app\models\work\AllowRemoteWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($allowRemote,'id','name');
    $params = [
    ];
    echo $form->field($model, "allow_remote_id")->dropDownList($items,$params);
    ?>

    <?= $form->field($model, 'hour_capacity')->textInput() ?>

    <?php
    $cont = \app\models\work\CertificatTypeWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($cont,'id','name');
    $params = [
    ];
    echo $form->field($model, 'certificat_type_id')->dropDownList($items,$params)->label('Итоговая форма контроля');
    ?>

    <div style="border: 1px solid #cccccc; border-radius: 5px; padding: 10px; margin-bottom: 10px">
        <?= $form->field($model, 'is_network')->checkbox(['onchange' => 'CheckNetwork(this)']) ?>

        <div id="contractNetwork" style="display: <?php echo $model->is_network == 0 ? 'none' : 'block' ?>">
        <?= $form->field($model, 'contractFile')->fileInput() ?>
        <?php
        if (strlen($model->contract) > 2)
            echo '<h5>Загруженный файл: '.Html::a($model->contract, \yii\helpers\Url::to(['training-program/get-file', 'fileName' => $model->contract, 'type' => 'contract'])).'&nbsp;&nbsp;&nbsp;&nbsp; '.Html::a('X', \yii\helpers\Url::to(['training-program/delete-file', 'fileName' => $model->contract, 'modelId' => $model->id, 'type' => 'contract'])).'</h5><br>';
        ?>
        </div>
    </div>

    <?= $form->field($model, 'description')->textarea(['rows' => '6', 'style' => ['resize' => 'none']])->label('Описание') ?>

    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'actual')->checkbox(); ?>

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
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['training-program/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'edit_docs'])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['training-program/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id])).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>



<script type="text/javascript">
    function CheckNetwork(main)
    {
        let elem = document.getElementById("contractNetwork");
        console.log(main.checked);
        if (main.checked) elem.style.display = "block";
        else elem.style.display = "none";
    }
</script>