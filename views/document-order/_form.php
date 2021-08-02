<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormAsset;
use wbraganca\dynamicform\DynamicFormWidget;


/* @var $this yii\web\View */
/* @var $model app\models\work\DocumentOrderWork */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$session = Yii::$app->session;
?>

<div class="document-order-form">

    <?php
    $model->people_arr = \app\models\work\PeopleWork::find()->select(['id as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>

    <?= $form->field($model, 'order_date')->widget(\yii\jui\DatePicker::class, [
        'dateFormat' => 'php:Y-m-d',
        'language' => 'ru',
        'options' => [
            'placeholder' => 'Дата документа',
            'class'=> 'form-control',
            'autocomplete'=>'off'
        ],
        'clientOptions' => [
            'changeMonth' => true,
            'changeYear' => true,
            'yearRange' => '2000:2050',
        ]])->label('Дата приказа') ?>


    <!---      -->
    <?php
    $params = [
        'id' => 'r',
        'onchange' => '
                        $.post(
                            "' . Url::toRoute('subattr') . '", 
                            {id: $(this).val()},
                            function(res){
                                var elems = document.getElementsByClassName("nom");
                                for (var c = 0; c !== elems.length; c++) {
                                    if (elems[c].id == "rS")
                                        elems[c].innerHTML = res;
                                }
                            }
                        );
                    ',
    ];

    $branch = \app\models\work\BranchWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($branch,'id','name');
    echo $form->field($model, 'nomenclature_id')->dropDownList($items,$params)->label('Отдел');

    ?>

    <?php
    $params = [
        'prompt' => '',
        'id' => 'rS',
        'class' => 'form-control nom',
    ];
    echo $form->field($model, 'order_number')->dropDownList([], $params)->label('Преамбула'); ?>

    <!---      -->

    <?= $form->field($model, 'order_name')->textInput(['maxlength' => true])->label('Наименование приказа') ?>

    <?php
    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
    ];
    echo $form->field($model, 'bring_id')->dropDownList($items,$params)->label('Проект вносит');

    ?>

    <?php
    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
    ];
    echo $form->field($model, 'executor_id')->dropDownList($items,$params)->label('Кто исполнил');

    ?>
    <br>
    <?php
    echo $form->field($model, 'allResp')
    ->checkbox([
        'label' => 'Добавить всех работников в ответственных',
        'labelOptions' => [
        ],
    ]);
    ?>
    <div class="row" style="overflow-y: scroll; height: 250px">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Ответственные</h4></div>
            <?php
            $resp = \app\models\work\ResponsibleWork::find()->where(['document_order_id' => $model->id])->all();
            if ($resp != null)
            {
                echo '<table>';
                foreach ($resp as $respOne) {
                    $respOnePeople = \app\models\work\PeopleWork::find()->where(['id' => $respOne->people_id])->one();
                    echo '<tr><td style="padding-left: 20px"><h4>'.$respOnePeople->secondname.' '.$respOnePeople->firstname.' '.$respOnePeople->patronymic.'</h4></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['document-order/delete-responsible', 'peopleId' => $respOnePeople->id, 'orderId' => $model->id])).'</td></tr>';
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
                    'model' => $modelResponsible[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'people_id',
                    ],
                ]); ?>

                <div class="container-items"><!-- widgetContainer -->
                    <?php foreach ($modelResponsible as $i => $modelResponsibleOne): ?>
                        <div class="item panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading" onload="scrolling()">
                                <h3 class="panel-title pull-left">Ответственный</h3>
                                <div class="pull-right">
                                    <button type="button" name="add" class="add-item btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body" id="scroll">
                                <?php
                                // necessary for update action.
                                if (! $modelResponsibleOne->isNewRecord) {
                                    echo Html::activeHiddenInput($modelResponsibleOne, "[{$i}]id");
                                }
                                ?>
                                <?php
                                $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
                                $items = \yii\helpers\ArrayHelper::map($people,'fullName','fullName');
                                $params = [
                                    'prompt' => ''
                                ];
                                echo $form->field($modelResponsibleOne, "[{$i}]fio")->dropDownList($items,$params)->label('ФИО');

                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Утратили силу документы</h4></div>
            <br>
            <?php
            $order = \app\models\work\ExpireWork::find()->where(['active_regulation_id' => $model->id])->all();
            if ($order != null)
            {
                echo '<table>';
                foreach ($order as $orderOne) {
                    if ($orderOne->expireRegulation !== null)
                        echo '<tr><td style="padding-left: 20px"><h4><b>Утратил силу документ: </b> Положение "'.$orderOne->expireRegulation->name.'"</h4></td><td style="padding-left: 10px">'
                            .Html::a('Отменить', \yii\helpers\Url::to(['document-order/delete-expire', 'expireId' => $orderOne->id, 'modelId' => $model->id]), [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => 'Вы уверены?',
                                    'method' => 'post',
                                ],]).'</td></tr>';
                    if ($orderOne->expireOrder !== null)
                        echo '<tr><td style="padding-left: 20px"><h4><b>Утратил силу документ: </b> Приказ №'.$orderOne->expireOrderWork->fullName.'"</h4></td><td style="padding-left: 10px">'
                            .Html::a('Отменить', \yii\helpers\Url::to(['document-order/delete-expire', 'expireId' => $orderOne->id, 'modelId' => $model->id]), [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => 'Вы уверены?',
                                    'method' => 'post',
                                ],]).'</td></tr>';
                }
                echo '</table>';
            }
            ?>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items1', // required: css class selector
                    'widgetItem' => '.item1', // required: css class
                    'limit' => 10, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item1', // css class
                    'deleteButton' => '.remove-item1', // css class
                    'model' => $modelExpire[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'id',
                    ],
                ]); ?>

                <div class="container-items1"><!-- widgetContainer -->
                    <?php foreach ($modelExpire as $i => $modelExpireOne): ?>
                        <div class="item1 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading">
                                <h3 class="panel-title pull-left">Приказ</h3>
                                <div class="pull-right">
                                    <button type="button" class="add-item1 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item1 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body">
                                <?php
                                // necessary for update action.
                                if (! $modelExpireOne->isNewRecord) {
                                    echo Html::activeHiddenInput($modelExpireOne, "[{$i}]id");
                                }
                                ?>
                                <?php
                                $orders = [];
                                if ($model->id == null)
                                    $orders = \app\models\work\DocumentOrderWork::find()->where(['!=', 'order_name', 'Резерв'])->all();
                                else
                                    $orders = \app\models\work\DocumentOrderWork::find()->where(['!=', 'order_name', 'Резерв'])->andWhere(['!=', 'id', $model->id])->all();
                                $items = \yii\helpers\ArrayHelper::map($orders,'id','fullName');
                                $params = [
                                    'prompt' => '',
                                ];

                                echo $form->field($modelExpireOne, "[{$i}]expire_order_id")->dropDownList($items,$params)->label('Приказ');
                                ?>

                                <?php
                                $orders = \app\models\work\RegulationWork::find()->all();
                                $items = \yii\helpers\ArrayHelper::map($orders,'id','name');
                                $params = [
                                    'prompt' => '',
                                ];

                                echo $form->field($modelExpireOne, "[{$i}]expire_regulation_id")->dropDownList($items,$params)->label('Положение');

                                ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <div style="display: none">
        <?php

        $value = false;
        if ($session->get('type') === '1') $value = true;

        if ($model->order_date === null)
            echo $form->field($model, 'type')->checkbox(['checked' => $value ? '' : null]);
        else
            echo $form->field($model, 'type')->checkbox();

        ?>
    </div>


    <?= $form->field($model, 'key_words')->textInput(['maxlength' => true])->label('Ключевые слова') ?>

    <?= $form->field($model, 'scanFile')->fileInput()->label('Скан приказа') ?>
    <?php
    if (strlen($model->scan) > 2)
        echo '<h5>Загруженный файл: '.Html::a($model->scan, \yii\helpers\Url::to(['document-order/get-file', 'fileName' => $model->scan])).'&nbsp;&nbsp;&nbsp;&nbsp; '.Html::a('X', \yii\helpers\Url::to(['document-order/delete-file', 'fileName' => $model->scan, 'modelId' => $model->id, 'type' => 'scan'])).'</h5><br>';
    ?>

    <?= $form->field($model, 'docFiles[]')->fileInput(['multiple' => true])->label('Редактируемые документы') ?>

    <?php
    if ($model->doc !== null)
    {
        $split = explode(" ", $model->doc);
        echo '<table>';
        for ($i = 0; $i < count($split) - 1; $i++)
        {
            echo '<tr><td><h5>Загруженный файл: '.Html::a($split[$i], \yii\helpers\Url::to(['document-order/get-file', 'fileName' => $split[$i]])).'</h5></td><td style="padding-left: 10px">'.Html::a('X', \yii\helpers\Url::to(['document-order/delete-file', 'fileName' => $split[$i], 'modelId' => $model->id])).'</td></tr>';
        }
        echo '</table>';
    }

    ?>

    <div class="form-group">
        <?= Html::submitButton('Добавить приказ', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
