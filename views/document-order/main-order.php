<?php

use app\models\work\NomenclatureWork;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormAsset;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\db\Query;

/* @var $this yii\web\View */
/* @var $model app\models\work\DocumentOrderWork */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$session = Yii::$app->session;
?>

<script>
    function showArchive()
    {
        var elem = document.getElementById('archive-0');
        var arch = document.getElementById('archive-number');
        var ord = document.getElementById('order-number-1');
        if (elem.checked) { arch.style.display = "block"; ord.style.display = "none"; }
        else { arch.style.display = "none"; ord.style.display = "block"; }
    }

    var enter_press = false
    function preventEnter(key)
    {
        if (key === 'Enter')
            enter_press = true;
        else
            enter_press = false;
        searchColumn();
        return !enter_press;
    }

    function clickSub() {
        enter_press = false;
    }

    function save()
    {
        //searchColumn();
        if (enter_press) {
            enter_press = !enter_press;
            return false;
        }
        return true;
    }
</script>

<div class="document-order-form">

    <?php

    $model->people_arr = \app\models\work\PeopleWork::find()->select(['id as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();

    $form = ActiveForm::begin(['id' => 'dynamic-form', 'options' => ['onsubmit' => 'save()']]); ?>

    <?php
        if ($model->id != NULL)
            echo $form->field($model, 'order_date')->textInput([ 'readonly' => true])->label('Дата приказа');
        else
            echo $form->field($model, 'order_date')->widget(\yii\jui\DatePicker::class, [
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
            ]])->label('Дата приказа');
    ?>


    <!---      -->
    <?php
    $model->nomenclature_id = '5';
    ?>

    <?php
    $params = [
        //'prompt' => '',
        'id' => 'rS',
        'class' => 'form-control nom',
        'onchange' => 'documentName()',
    ];
    if ($model->type !== 10)
    {
        echo '<div id="order-number-1">';
        $noms = \app\models\work\NomenclatureWork::find()->where(['branch_id' => $model->nomenclature_id])->andWhere(['actuality' => 0])->all();
        $items = \yii\helpers\ArrayHelper::map($noms,'number','fullNameWork');
        echo $form->field($model, 'order_number')->dropDownList($items, $params)->label('Код и описание номенклатуры');
        echo '</div>';
    }
    ?>

    <div id="archive-block" style="display: block;">
        <?= $form->field($model, 'archive_check')
            ->checkbox([
                'id' => 'archive-0',
                'label' => 'Архивный приказ',
                'onchange' => 'showArchive()',
                'checked' => $model->type === 10,
                'labelOptions' => [
                ],
            ]); ?>
    </div>

    <div id="archive-number" style="display: <?php echo $model->type === 10 ? 'block' : 'none'; ?>">
        <?= $form->field($model, 'archive_number')->textInput()->label('Архивный номер'); ?>
    </div>

    <?php
    echo $form->field($model, 'order_name')->textInput(['maxlength' => true])->label('Наименование приказа');
    ?>

    <?php
    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
        'prompt' => '',
    ];
    echo $form->field($model, 'bring_id')->dropDownList($items,$params)->label('Проект вносит');

    ?>

    <?php
    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
        'prompt' => '',
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
    <div class="row" style="overflow-y: scroll; height: 270px">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Ответственные</h4></div>
            <div>
                <?php
                $resp = \app\models\work\ResponsibleWork::find()->where(['document_order_id' => $model->id])->all();
                if ($resp != null)
                {
                    echo '<table>';
                    foreach ($resp as $respOne) {
                        $respOnePeople = \app\models\work\PeopleWork::find()->where(['id' => $respOne->people_id])->one();
                        echo '<tr><td style="padding-left: 20px"><h4>'.$respOnePeople->secondname.' '.$respOnePeople->firstname.' '.$respOnePeople->patronymic.'</h4></td><td style="padding-left: 10px">'
                            .Html::a('Удалить', \yii\helpers\Url::to(['document-order/delete-responsible', 'peopleId' => $respOnePeople->id, 'orderId' => $model->id]), [
                                'class' => 'btn btn-danger',
                                'data' => [
                                    'confirm' => 'Вы уверены?',
                                    'method' => 'post',
                                ],]).'</td></tr>';
                    }
                    echo '</table>';
                }
                ?>
            </div>
            <div class="panel-body">
                <?php DynamicFormWidget::begin([
                    'widgetContainer' => 'dynamicform_wrapper5', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                    'widgetBody' => '.container-items5', // required: css class selector
                    'widgetItem' => '.item5', // required: css class
                    'limit' => 40, // the maximum times, an element can be cloned (default 999)
                    'min' => 1, // 0 or 1 (default 1)
                    'insertButton' => '.add-item5', // css class
                    'deleteButton' => '.remove-item5', // css class
                    'model' => $modelResponsible[0],
                    'formId' => 'dynamic-form',
                    'formFields' => [
                        'people_id',
                    ],
                ]); ?>

                <div class="container-items5"><!-- widgetContainer -->
                    <?php foreach ($modelResponsible as $i => $modelResponsibleOne): ?>
                        <div class="item5 panel panel-default"><!-- widgetBody -->
                            <div class="panel-heading" onload="scrolling()">
                                <h3 class="panel-title pull-left">Ответственный</h3>
                                <div class="pull-right">
                                    <button type="button" name="add" class="add-item5 btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                    <button type="button" class="remove-item5 btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="panel-body" id="scroll">
                                <?php
                                // necessary for update action.
                                if (!$modelResponsibleOne->isNewRecord) {
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
    <div id="change" class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Изменение документов</h4></div>
            <br>
            <?php
            //$order = \app\models\work\ExpireWork::find()->where(['active_regulation_id' => $model->id])->andWhere(['expire_type' => 1])->all();
            $order = \app\models\work\ExpireWork::find()->where(['active_regulation_id' => $model->id])->all();
            if ($order != null)
            {
                echo '<table>';
                foreach ($order as $orderOne) {
                    if ($orderOne->expireRegulation !== null)
                        if ($orderOne->expire_type === 1)
                            echo '<tr><td style="padding-left: 20px; width: 90%;"><h4><b>Отменяет документ: </b> Положение "'.$orderOne->expireRegulationWork->name.'"</h4></td><td style="padding-left: 10px">'
                                .Html::a('Отменить', \yii\helpers\Url::to(['document-order/delete-expire', 'expireId' => $orderOne->id, 'modelId' => $model->id]), [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Вы уверены?',
                                        'method' => 'post',
                                    ],]).'</td></tr>';
                        else if ($orderOne->expire_type === 2)
                            echo '<tr><td style="padding-left: 20px; width: 90%;"><h4><b>Изменяет документ: </b> Положение "' . $orderOne->expireRegulationWork->name . '"</h4></td><td style="padding-left: 10px">'
                                . Html::a('Отменить', \yii\helpers\Url::to(['document-order/delete-expire', 'expireId' => $orderOne->id, 'modelId' => $model->id]), [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Вы уверены?',
                                        'method' => 'post',
                                    ],]) . '</td></tr>';
                    if ($orderOne->expireOrder !== null)
                        if ($orderOne->expire_type === 1)
                            echo '<tr><td style="padding-left: 20px; width: 90%;"><h4><b>Отменяет документ: </b> Приказ №'.$orderOne->expireOrderWork->fullName.'"</h4></td><td style="padding-left: 10px">'
                                .Html::a('Отменить', \yii\helpers\Url::to(['document-order/delete-expire', 'expireId' => $orderOne->id, 'modelId' => $model->id]), [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Вы уверены?',
                                        'method' => 'post',
                                    ],]).'</td></tr>';
                        else if ($orderOne->expire_type === 2)
                            echo '<tr><td style="padding-left: 20px; width: 90%;"><h4><b>Изменяет документ: </b> Приказ №' . $orderOne->expireOrderWork->fullName . '"</h4></td><td style="padding-left: 10px">'
                                . Html::a('Отменить', \yii\helpers\Url::to(['document-order/delete-expire', 'expireId' => $orderOne->id, 'modelId' => $model->id]), [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Вы уверены?',
                                        'method' => 'post',
                                    ],]) . '</td></tr>';
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
                                <div class="col-xs-5">
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
                                </div>
                                <div class="col-xs-5">
                                    <?php
                                    $orders = \app\models\work\RegulationWork::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($orders,'id','name');
                                    $params = [
                                        'prompt' => '',
                                    ];

                                    echo $form->field($modelExpireOne, "[{$i}]expire_regulation_id")->dropDownList($items,$params)->label('Положение');

                                    ?>
                                </div>
                                <div class="col-xs-2">
                                    <?php
                                    $arr = ['1' => 'Отмена', '2' => 'Изменение'];
                                    if ($modelExpireOne->expire_type === null)
                                        $modelExpireOne->expire_type = 1;
                                    echo $form->field($modelExpireOne, "[{$i}]expire_type")->radioList($arr,[])->label(false);
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
        <?php echo Html::submitButton('Сохранить приказ', ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
