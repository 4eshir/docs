<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\ForeignEvent */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="foreign-event-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php
    $company = \app\models\common\Company::find()->all();
    $items = \yii\helpers\ArrayHelper::map($company,'id','name');
    $params = [
    ];
    echo $form->field($model, 'company_id')->dropDownList($items,$params);

    ?>

    <?= $form->field($model, 'start_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата начала мероприятия',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]]) ?>

    <?= $form->field($model, 'finish_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата окончания мероприятия',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]]) ?>

    <?= $form->field($model, 'city')->textInput(['maxlength' => true]) ?>

    <?php
    $ways = \app\models\common\EventWay::find()->all();
    $items = \yii\helpers\ArrayHelper::map($ways,'id','name');
    $params = [
    ];
    echo $form->field($model, 'event_way_id')->dropDownList($items,$params);

    ?>

    <?php
    $levels = \app\models\common\EventLevel::find()->all();
    $items = \yii\helpers\ArrayHelper::map($levels,'id','name');
    $params = [
    ];
    echo $form->field($model, 'event_level_id')->dropDownList($items,$params);

    ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-user"></i>Участники</h4></div>
            <?php
            /*$resp = \app\models\common\Responsible::find()->where(['document_order_id' => $model->id])->all();
            if ($resp != null)
            {
                echo '<table>';
                foreach ($resp as $respOne) {
                    $respOnePeople = \app\models\common\People::find()->where(['id' => $respOne->people_id])->one();
                    echo '<tr><td style="padding-left: 20px"><h4>'.$respOnePeople->secondname.' '.$respOnePeople->firstname.' '.$respOnePeople->patronymic.'</h4></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['document-order/delete-responsible', 'peopleId' => $respOnePeople->id, 'orderId' => $model->id])).'</td></tr>';
                }
                echo '</table>';
            }*/
            ?>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 50, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelParticipants[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'people_id',
                    ],
                ]); ?>

                <div class="container-items" style="padding: 0; margin: 0"><!-- widgetContainer -->
                    <?php foreach ($modelParticipants as $i => $modelParticipantsOne): ?>
                        <div class="item panel panel-default" style="padding: 0; margin: 0"><!-- widgetBody -->
                            <div class="panel-heading" style="padding: 0; margin: 0">
                                <div class="pull-right">
                                    <button type="button" name="add" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-4">
                                <?php
                                $people = \app\models\common\ForeignEventParticipants::find()->all();
                                $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                $params = [
                                    'prompt' => ''
                                ];
                                echo $form->field($modelParticipantsOne, "[{$i}]fio")->dropDownList($items,$params)->label('ФИО участника');

                                ?>
                            </div>
                            <div class="col-xs-4">
                                <?php
                                $people = \app\models\common\People::find()->where(['company_id' => 8])->all();
                                $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                $params = [
                                    'prompt' => ''
                                ];
                                echo $form->field($modelParticipantsOne, "[{$i}]teacher")->dropDownList($items,$params)->label('ФИО педагога');

                                ?>
                            </div>
                            <div class="col-xs-4">
                                <?= $form->field($modelParticipantsOne, "[{$i}]file")->fileInput()->label('Заявка') ?>
                            </div>
                            <div class="panel-body" style="padding: 0; margin: 0"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'min_participants_age')->textInput() ?>

    <?= $form->field($model, 'max_participants_age')->textInput() ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-sunglasses"></i>Победители и призеры</h4></div>
            <?php
            /*$resp = \app\models\common\Responsible::find()->where(['document_order_id' => $model->id])->all();
            if ($resp != null)
            {
                echo '<table>';
                foreach ($resp as $respOne) {
                    $respOnePeople = \app\models\common\People::find()->where(['id' => $respOne->people_id])->one();
                    echo '<tr><td style="padding-left: 20px"><h4>'.$respOnePeople->secondname.' '.$respOnePeople->firstname.' '.$respOnePeople->patronymic.'</h4></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['document-order/delete-responsible', 'peopleId' => $respOnePeople->id, 'orderId' => $model->id])).'</td></tr>';
                }
                echo '</table>';
            }*/
            ?>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items1', // required: css class selector
                    'widgetItem' => '.item1', // required: css class
                    'limit' => 50, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item1', // css class
                    'deleteButton' => '.remove-item1', // css class
                    'model' => $modelAchievement[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'people_id',
                    ],
                ]); ?>

                <div class="container-items1" style="padding: 0; margin: 0"><!-- widgetContainer -->
                    <?php foreach ($modelAchievement as $i => $modelAchievementOne): ?>
                        <div class="item1 panel panel-default" style="padding: 0; margin: 0"><!-- widgetBody -->
                            <div class="panel-heading" style="padding: 0; margin: 0">
                                <div class="pull-right">
                                    <button type="button" name="add" class="add-item1 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item1 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="col-xs-6">
                                <?php
                                $people = \app\models\common\ForeignEventParticipants::find()->all();
                                $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                $params = [
                                    'prompt' => ''
                                ];
                                echo $form->field($modelAchievementOne, "[{$i}]fio")->dropDownList($items,$params)->label('ФИО участника');

                                ?>
                            </div>
                            <div class="col-xs-6">
                                <?php

                                echo $form->field($modelAchievementOne, "[{$i}]achieve")->textInput();

                                ?>
                            </div>
                            <div class="panel-body" style="padding: 0; margin: 0"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <?= $form->field($model, 'business_trip')->checkbox(['id' => 'tripCheckbox', 'onchange' => 'checkTrip()']) ?>

    <div id="divEscort" hidden>
        <?php
        $people = \app\models\common\People::find()->where(['company_id' => 8])->all();
        $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
        $params = [
        ];
        echo $form->field($model, 'escort_id')->dropDownList($items,$params);

        ?>
    </div>

    <div id="divOrderTrip" hidden>
        <?php
        $orders = \app\models\common\DocumentOrder::find()->all();
        $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
        $params = [
        ];
        echo $form->field($model, 'order_business_trip_id')->dropDownList($items,$params);

        ?>
    </div>

    <?php
    $orders = \app\models\common\DocumentOrder::find()->all();
    $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
    $params = [
    ];
    echo $form->field($model, 'order_participation_id')->dropDownList($items,$params);

    ?>

    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'docs_achievement')->fileInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script>
    function checkTrip()
    {
        var chkBox = document.getElementById('tripCheckbox');
        if (chkBox.checked)
        {
            $("#divEscort").removeAttr("hidden");
            $("#divOrderTrip").removeAttr("hidden");
        }
        else
        {
            $("#divEscort").attr("hidden", "true");
            $("#divOrderTrip").attr("hidden", "true");
        }
    }
</script>