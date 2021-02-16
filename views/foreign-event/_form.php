<?php

use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\common\ForeignEvent */
/* @var $form yii\widgets\ActiveForm */
?>

<style type="text/css">
    .button {
        position: fixed;
        bottom: 0px;
        background-color: #f5f8f9;
        width: 77%;
        padding-left: 1%;
        padding-top: 1%;
        padding-right: 1%;
        padding-bottom: 1%; /*104.5px is half of the button width*/
    }
    .test{
        height:1000px;

    }
</style>

<div class="foreign-event-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true, 'disabled' => $model->copy == 1 ? 'disabled' : 'enabled']) ?>

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
            'autocomplete'=>'off',
            'disabled' => $model->copy == 1 ? 'disabled' : 'enabled'
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
            'autocomplete'=>'off',
            'disabled' => $model->copy == 1 ? 'disabled' : 'enabled'
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
        'disabled' => $model->copy == 1 ? 'disabled' : 'enabled'
    ];
    echo $form->field($model, 'event_level_id')->dropDownList($items,$params);

    ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-user"></i>Участники</h4></div>
            <?php
            $parts = \app\models\common\TeacherParticipant::find()->where(['foreign_event_id' => $model->id])->all();
            if ($parts != null)
            {
                echo '<table class="table table-bordered">';
                echo '<tr><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Участник</b></h4></td><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Педагог</b></h4></td></td><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Команда</b></h4></td><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Представленные материалы</b></h4></td></tr>';
                foreach ($parts as $partOne) {
                    $partOnePeople = \app\models\common\ForeignEventParticipants::find()->where(['id' => $partOne->participant_id])->one();
                    $partFiles = \app\models\common\ParticipantFiles::find()->where(['participant_id' => $partOnePeople->id])->andWhere(['foreign_event_id' => $partOne->foreign_event_id])->one();
                    $partOneTeacher = \app\models\common\People::find()->where(['id' => $partOne->teacher_id])->one();
                    $team = \app\models\common\Team::find()->where(['foreign_event_id' => $model->id])->andWhere(['participant_id' => $partOnePeople->id])->one();
                    echo '<tr><td style="padding-left: 20px"><h4>'.
                            $partOnePeople->shortName.'&nbsp;</label>'.'</h4></td><td style="padding-left: 20px"><h4>'.$partOneTeacher->shortName.'</h4></td>'.
                            '<td style="padding-left: 10px">'.$team->name.'</td>'.
                            '<td><h5>'.Html::a($partFiles->filename, \yii\helpers\Url::to(['foreign-event/get-file', 'fileName' => $partFiles->filename, 'type' => 'participants'])).'</h5></td>'.
                            '<td style="padding-left: 10px">'.Html::a('Удалить', \yii\helpers\Url::to(['foreign-event/delete-participant', 'id' => $partOne->id, 'model_id' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                }
                echo '</table>';
            }
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
                                <div>
                                    <?php
                                    $people = \app\models\common\ForeignEventParticipants::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                    $params = [
                                        'prompt' => ''
                                    ];
                                    echo $form->field($modelParticipantsOne, "[{$i}]fio")->dropDownList($items,$params)->label('ФИО участника');
                                    $branchs = \app\models\common\Branch::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($branchs, 'id', 'name');
                                    echo $form->field($modelParticipantsOne, "[{$i}]branch")->dropDownList($items,$params)->label('Отдел');
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div>
                                    <?php
                                    $people = \app\models\common\People::find()->where(['company_id' => 8])->all();
                                    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                    $params = [
                                        'prompt' => ''
                                    ];
                                    echo $form->field($modelParticipantsOne, "[{$i}]teacher")->dropDownList($items,$params)->label('ФИО педагога');
                                    echo $form->field($modelParticipantsOne, "[{$i}]focus")->textInput()->label('Направленность');
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div>
                                    <?= $form->field($modelParticipantsOne, "[{$i}]file")->fileInput()->label('Представленные материалы') ?>
                                    <?php
                                    $people = \app\models\common\ParticipantFiles::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($people,'filename','filename');
                                    $params = [
                                    'prompt' => ''
                                    ];
                                    echo $form->field($modelParticipantsOne, "[{$i}]file")->dropDownList($items,$params)->label(false);
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div>
                                    <?= $form->field($modelParticipantsOne, "[{$i}]team")->label('В составе команды'); ?>
                                </div>
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
            $parts = \app\models\common\ParticipantAchievement::find()->where(['foreign_event_id' => $model->id])->all();
            if ($parts != null)
            {
                echo '<table class="table table-bordered">';
                echo '<tr><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Участник</b></h4></td><td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Достижение</b></h4></td></tr>';
                foreach ($parts as $partOne) {
                    $partOnePeople = \app\models\common\ForeignEventParticipants::find()->where(['id' => $partOne->participant_id])->one();
                    echo '<tr><td style="padding-left: 20px"><h4>'.$partOnePeople->shortName.'</h4></td><td style="padding-left: 20px"><h4>'.$partOne->achievment.'</h4></td><td style="padding-left: 10px">'.Html::a('Удалить', \yii\helpers\Url::to(['foreign-event/delete-achievement', 'id' => $partOne->id, 'model_id' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                }
                echo '</table>';
            }
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
                            <div class="col-xs-4">
                                <?php
                                $people = \app\models\common\ForeignEventParticipants::find()->all();
                                $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                $params = [
                                    'prompt' => ''
                                ];
                                echo $form->field($modelAchievementOne, "[{$i}]fio")->dropDownList($items,$params)->label('ФИО участника');

                                ?>
                            </div>
                            <div class="col-xs-4">
                                <?php

                                echo $form->field($modelAchievementOne, "[{$i}]achieve")->textInput();

                                ?>
                            </div>
                            <div class="col-xs-4">
                                <?php

                                echo $form->field($modelAchievementOne, "[{$i}]winner")->checkbox();

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

    <div id="divEscort" <?php echo $model->business_trip == 0 ? 'hidden' : '' ?>>
        <?php
        $people = \app\models\common\People::find()->where(['company_id' => 8])->all();
        $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
        $params = [
        ];
        echo $form->field($model, 'escort_id')->dropDownList($items,$params);

        ?>
    </div>

    <div id="divOrderTrip" <?php echo $model->business_trip == 0 ? 'hidden' : '' ?>>
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

    <?= $form->field($model, 'docsAchievement')->fileInput() ?>

    <?php
    if ($model->docs_achievement !== null)
        echo '<h5>Загруженный файл: '.Html::a($model->docs_achievement, \yii\helpers\Url::to(['foreign-event/get-file', 'fileName' => $model->docs_achievement, 'type' => 'achievements_files'])).'</h5><br>';
    ?>
    <div class="form-group">
        <div class="button">

            <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success',
                'data' => [
                    'confirm' => 'Сохранить изменения? Если были загружены новые файлы заявок/достижений, то они заменят более старые',
                    'method' => 'post',
                ],]) ?>
        </div>
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