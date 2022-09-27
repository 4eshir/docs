<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;

/* @var $this yii\web\View */
/* @var $model app\models\common\Container */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="container-form">

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>

    <?php
    $containers = \app\models\work\ContainerWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($containers,'id','name');
    $params = [
        'prompt' => '',
        'style' => 'width: 50%',
        'id' => 'link_container',
        'onchange' => 'CheckLink()',
    ];
    echo $form->field($model, 'container_id')->dropDownList($items,$params);

    ?>

    <?php
    $objects = \app\models\work\MaterialObjectWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($objects,'id','name');
    $params = [
        'prompt' => '',
        'style' => 'width: 50%',
        'id' => 'link_object',
        'onchange' => 'CheckLink()',
    ];
    echo $form->field($model, 'material_object_id')->dropDownList($items,$params);

    ?>

    <?php
    $auds = \app\models\work\AuditoriumWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($auds,'id','name');
    $params = [
        'prompt' => '',
        'style' => 'width: 50%',
        'id' => 'link_auditorium',
        'onchange' => 'CheckLink()',
    ];
    echo $form->field($model, 'auditorium_id')->dropDownList($items,$params);

    ?>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4>Список объектов в контейнере</h4></div>
            <div>
                <?php
                $objects = \app\models\work\ContainerObjectWork::find()->where(['container_id' => $model->id])->all();
                if ($objects != null)
                {
                    echo '<table class="table table-bordered" style="margin: 15px; width: 90%">';
                    echo '<tr><td><b>Объект</b></td><td><b>Количество</b></td><td></td></tr>';
                    foreach ($objects as $object) {
                        echo '<tr><td><h5>'.$object->materialObjectWork->name.'</h5></td><td>'.$object->materialObjectWork->count.'</td><td>'.Html::a('Удалить', \yii\helpers\Url::to(['container/delete-object', 'id' => $object->id, 'modelId' => $model->id]), ['class' => 'btn btn-danger']).'</td></tr>';
                    }
                    echo '</table>';
                }
                ?>
            </div>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items', // required: css class selector
                    'widgetItem' => '.item', // required: css class
                    'limit' => 100, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item', // css class
                    'deleteButton' => '.remove-item', // css class
                    'model' => $modelObject[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'eventExternalName',
                    ],
                ]); ?>

                <div class="container-items" ><!-- widgetContainer -->
                    <?php foreach ($modelObject as $i => $modelObjectOne): ?>
                        <div class="item panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Объект</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <div class="col-xs-4">
                                    <?php
                                    $notIcludeObjects = \app\models\work\ContainerWork::find()->where(['not', ['material_object_id' => null]])->all();
                                    $nioIds = [];
                                    foreach ($notIcludeObjects as $object) $nioIds[] = $object->material_object_id;

                                    $objects = \app\models\work\MaterialObjectWork::find()->where(['NOT IN', 'id', $nioIds])->orderBy(['name' => SORT_ASC])->all();
                                    $items = \yii\helpers\ArrayHelper::map($objects,'id','name');
                                    $params = [
                                        'prompt' => '',
                                        'style' => 'width: 200%'
                                    ];
                                    echo $form->field($modelObjectOne, "[{$i}]material_object_id")->dropDownList($items,$params);

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

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success', 'id' => 'main_submit']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<script type="text/javascript">
    function CheckLink()
    {
        let el1 = document.getElementById("link_container");
        let el2 = document.getElementById("link_object");
        let el3 = document.getElementById("link_auditorium");

        let but = document.getElementById('main_submit');

        if (el1.value+el2.value+el3.value == '')
            but.setAttribute('disabled', 'disabled');
        else
            but.removeAttribute('disabled');
    }
</script>
