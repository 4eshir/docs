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

<script>
    window.onload = function() {
        initData();
    }


    var getUrlParameter = function getUrlParameter(sParam) {
        var sPageURL = decodeURIComponent(window.location.search.substring(1)),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;

        for (i = 0; i < sURLVariables.length; i++) {
            sParameterName = sURLVariables[i].split('=');

            if (sParameterName[0] === sParam) {
                return sParameterName[1] === undefined ? true : sParameterName[1];
            }
        }
    };

    function showArchive()
    {
        var elem = document.getElementById('archive-0');
        var arch = document.getElementById('archive-number');
        var ord = document.getElementById('order-number-1');
        if (elem.checked) { arch.style.display = "block"; ord.style.display = "none"; }
        else { arch.style.display = "none"; ord.style.display = "block"; }
    }

    const initData = () => {
        table = document.getElementById('sortable');
        headers = table.querySelectorAll('th');
        tableBody = table.querySelector('tbody');
        rows = tableBody.querySelectorAll('tr');

        // Направление сортировки
        directions = Array.from(headers).map(function(header) {
            return '';
        });

        // Преобразовать содержимое данной ячейки в заданном столбце
        transform = function(index, content) {
            // Получить тип данных столбца
            const type = headers[index].getAttribute('data-type');
            switch (type) {
                case 'number':
                    return parseFloat(content);
                case 'string':
                default:
                    return content;
            }
        };
    }

    let table = '';
    let headers = '';
    let tableBody = '';
    let rows = '';
    let directions = '';
    let transform = '';

    function sortColumn(index) {
        // Получить текущее направление
        const direction = directions[index] || 'asc';

        // Фактор по направлению
        const multiplier = (direction === 'asc') ? 1 : -1;

        const newRows = Array.from(rows);

        newRows.sort(function(rowA, rowB) {
            const cellA = rowA.querySelectorAll('td')[index].innerHTML;
            const cellB = rowB.querySelectorAll('td')[index].innerHTML;

            const a = transform(index, cellA);
            const b = transform(index, cellB);

            switch (true) {
                case a > b: return 1 * multiplier;
                case a < b: return -1 * multiplier;
                case a === b: return 0;
            }
        });

        // Удалить старые строки
        [].forEach.call(rows, function(row) {
            tableBody.removeChild(row);
        });

        // Поменять направление
        directions[index] = direction === 'asc' ? 'desc' : 'asc';

        // Добавить новую строку
        newRows.forEach(function(newRow) {
            tableBody.appendChild(newRow);
        });

    }

    function searchColumn() {
        var inputName, filterName, inputLeftDate, filterLeftDate, inputRightDate, filterRightDate, td, tdName, tdLeftDate, tdRightDate, i, txtValueName, txtValueLeftDate, txtValueRightDate;

        inputName = document.getElementById('nameSearch');
        filterName = inputName.value.toUpperCase();
        inputLeftDate = document.getElementById('nameLeftDate');
        filterLeftDate = inputLeftDate.value.toUpperCase();
        inputRightDate = document.getElementById('nameRightDate');
        filterRightDate = inputRightDate.value.toUpperCase();

        for (i = 0; i < rows.length; i++)
        {
            td = rows[i].getElementsByTagName("td");
            tdName = td[1];
            tdLeftDate = td[2];
            tdRightDate = td[3];

            if (td) {
                txtValueName = tdName.textContent || tdName.innerText;
                txtValueLeftDate = tdLeftDate.textContent || tdLeftDate.innerText;
                txtValueRightDate = tdRightDate.textContent || tdRightDate.innerText;

                if (filterRightDate == '')
                    filterRightDate = '2100-12-12';

                if (txtValueName.toUpperCase().indexOf(filterName) > -1 && txtValueLeftDate.toUpperCase() >= filterLeftDate && txtValueRightDate.toUpperCase() <= filterRightDate)
                    rows[i].style.display = "";
                else
                    rows[i].style.display = "none";
            }
        }
    }
</script>

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
        'prompt' => '--',
        'id' => 'r',
        'onchange' => '
                        $.post(
                            "' . Url::toRoute('subattr') . '", 
                            {id: $(this).val(),
                            idG: getUrlParameter("id")},
                            function(res){
                                var resArr = res.split("|split|");
                                var elems = document.getElementsByClassName("nom");
                                for (var c = 0; c !== elems.length; c++) {
                                    if (elems[c].id == "rS")
                                        elems[c].innerHTML = resArr[0];
                                }
                                var elem = document.getElementById("group_table");
                                elem.innerHTML = resArr[1];
                                initData();
                            }
                        );
                    ',
    ];

    $branch = \app\models\work\BranchWork::find()->orderBy(['name' => SORT_ASC])->all();
    $items = \yii\helpers\ArrayHelper::map($branch,'id','name');
    if ($session->get('type') != 1)
        echo $form->field($model, 'nomenclature_id')->dropDownList($items,$params)->label('Отдел');
    else
        $model->nomenclature_id = '5';  // по просьбе особ упоротых людителей делать "случайно" основные приказы вне отдела Администрация - по умолчанию делаем как надо
    ?>

    <?php
    $params = [
        //'prompt' => '',
        'id' => 'rS',
        'class' => 'form-control nom',
    ];
    if ($model->type !== 10)
    {
        echo '<div id="order-number-1">';
        if ($model->nomenclature_id === null)
            echo $form->field($model, 'order_number')->dropDownList([], $params)->label('Преамбула');
        else
        {
            $noms = \app\models\work\NomenclatureWork::find()->where(['branch_id' => $model->nomenclature_id])->all();
            $items = \yii\helpers\ArrayHelper::map($noms,'number','fullNameWork');
            echo $form->field($model, 'order_number')->dropDownList($items, $params)->label('Преамбула');
        }
        echo '</div>';
    }
    ?>

    <?= $form->field($model, 'archive_check')
        ->checkbox([
            'id' => 'archive-0',
            'label' => 'Архивный приказ',
            'onchange' => 'showArchive()',
            'checked' => $model->type === 10,
            'labelOptions' => [
            ],
        ]); ?>

    <div id="archive-number" style="display: <?php echo $model->type === 10 ? 'block' : 'none'; ?>">

        <?= $form->field($model, 'archive_number')->textInput()->label('Архивный номер'); ?>
    </div>

    <div id="group_table" style="margin-bottom: 1em;" <?php echo $session->get('type') === '1' ? 'hidden' : null ?>>
        <?php
        echo '<b>Фильтры для учебных групп: </b>';
        echo '<input type="text" id="nameSearch" onchange="searchColumn()" placeholder="Поиск по части имени..." title="Введите имя">';
        echo '    С <input type="date" id="nameLeftDate" onchange="searchColumn()" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="Поиск по дате начала занятий...">';
        echo '    По <input type="date" id="nameRightDate" onchange="searchColumn()" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="Поиск по дате начала занятий...">';

        if ($model->nomenclature_id !== null) {
            echo '<div style="max-height: 400px; overflow-y: scroll; margin-top: 1em;"><table id="sortable" class="table table-bordered"><thead><tr><th></th><th><a onclick="sortColumn(1)"><b>Учебная группа</b></a></th><th><a onclick="sortColumn(2)"><b>Дата начала занятий</b></a></th><th><a onclick="sortColumn(3)"><b>Дата окончания занятий</b></a></th></tr></thead>';
            echo '';
            echo '<tbody>';
            $groups = \app\models\work\TrainingGroupWork::find()->where(['order_stop' => 0])->andWhere(['archive' => 0])->andWhere(['branch_id' => $model->nomenclature_id])->all();
            foreach ($groups as $group)
            {
                $orders = \app\models\work\OrderGroupWork::find()->where(['training_group_id' => $group->id])->andWhere(['document_order_id' => $model->id])->one();
                echo '<tr><td style="width: 10px">';
                if ($orders !== null)
                    echo '<input type="checkbox" checked="true" id="documentorderwork-groups_check" name="DocumentOrderWork[groups_check][]" value="'.$group->id.'">';
                else
                    echo '<input type="checkbox" id="documentorderwork-groups_check" name="DocumentOrderWork[groups_check][]" value="'.$group->id.'">';
                echo '</td><td style="width: auto">';
                echo $group->number;
                echo '</td>';
                echo '</td><td style="width: auto">';
                echo $group->start_date;
                echo '</td>';
                echo '</td><td style="width: auto">';
                echo $group->finish_date;
                echo '</td></tr>';
            }

            echo '</tbody></table></div>';
        }
        ?>
    </div>

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
            <div>
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
    <div class="row" <?php echo $session->get('type') !== '1' ? 'hidden' : null ?>>
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i>Изменение документов</h4></div>
            <br>
            <?php
            $order = \app\models\work\ExpireWork::find()->where(['active_regulation_id' => $model->id])->andWhere(['expire_type' => 1])->all();
            if ($order != null)
            {
                echo '<table>';
                foreach ($order as $orderOne) {
                    if ($orderOne->expireRegulation !== null)
                        echo '<tr><td style="padding-left: 20px"><h4><b>Утратил силу документ: </b> Положение "'.$orderOne->expireRegulationWork->name.'"</h4></td><td style="padding-left: 10px">'
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

                $order = \app\models\work\ExpireWork::find()->where(['active_regulation_id' => $model->id])->andWhere(['expire_type' => 2])->all();
                if ($order != null) {
                    foreach ($order as $orderOne) {
                        if ($orderOne->expireRegulation !== null)
                            echo '<tr><td style="padding-left: 20px"><h4><b>Изменен документ: </b> Положение "' . $orderOne->expireRegulationWork->name . '"</h4></td><td style="padding-left: 10px">'
                                . Html::a('Отменить', \yii\helpers\Url::to(['document-order/delete-expire', 'expireId' => $orderOne->id, 'modelId' => $model->id]), [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Вы уверены?',
                                        'method' => 'post',
                                    ],]) . '</td></tr>';
                        if ($orderOne->expireOrder !== null)
                            echo '<tr><td style="padding-left: 20px"><h4><b>Изменен документ: </b> Приказ №' . $orderOne->expireOrderWork->fullName . '"</h4></td><td style="padding-left: 10px">'
                                . Html::a('Отменить', \yii\helpers\Url::to(['document-order/delete-expire', 'expireId' => $orderOne->id, 'modelId' => $model->id]), [
                                    'class' => 'btn btn-danger',
                                    'data' => [
                                        'confirm' => 'Вы уверены?',
                                        'method' => 'post',
                                    ],]) . '</td></tr>';
                    }
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
        <?php echo Html::submitButton('Добавить приказ', ['class' => 'btn btn-success']); ?>
    </div>

    <?php ActiveForm::end(); ?>
</div>
