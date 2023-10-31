<?php

use app\models\work\NomenclatureWork;
use app\models\work\TeacherParticipantWork;
use app\models\work\TeamNameWork;
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

<style>
    div[role=radiogroup] > label {
        font-weight: normal;
    }

    .row {
        margin: 0;
    }

    .main-div{
        margin: 30px 0;
        margin-bottom: 20px;
        background-color: #fff;
        border: 1px solid #ddd;
        border-radius: 4px;
        -webkit-box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
        box-shadow: 0 1px 1px rgba(0, 0, 0, 0.05);
    }

    .nomination-div{
        margin-bottom: 10px;
        height: 100%;
    }

    .nomination-list-div, .team-list-div {
        border: 1px solid #ccc;
        border-radius: 7px;
        padding: 10px;
        overflow-y: scroll;
        width: 47%;
        margin: 10px;
        height: 250px;
        display: inline-block;
    }

    .nomination-heading {
        padding: 10px;
        margin-bottom: 10px;
        background-color: #f5f5f5;
        border-color: #ddd;
        border-bottom: 1px solid #ddd;
    }

    .nomination-add-div{
        border: 1px solid #ddd;
        border-radius: 7px;
        padding: 0.5% 10px;
        margin: 10px;
        background-color: #f5f5f5;
        height: 80px;
        display: flex;
    }

    .nomination-add-input-div, .team-add-input-div {
        display: inline-block;
        vertical-align: top;
        height: 100%;
        width: 35%;
    }

    .nomination-add-button-div, .team-add-button-div {
        display: inline-block;
        padding: 1%;
        vertical-align: top;
        height: 100%;
        margin-left: -10px;
    }

    .nomination-add-button, .team-add-button{
        display: block;
        margin: 7px 10px;
        word-break: keep-all;
        line-height: 1.3rem;
        width: 100px;
    }

    .nomination-add-input, .team-add-input {
        display: block;
        margin: 0;
        padding: 0;
        width: 100%;
    }

    .nomination-label-input, .team-label-input {
        padding-left: 15px;
        margin-bottom: 0;
        width: 100%;
    }

    .nomination-list-item, .team-list-item {
        display: inline-block;
    }

    .nomination-list-row, .team-list-row {
        display: block;
    }

    .nomination-list-item-delete,  .team-list-item-delete {
        display: inline-block;
        margin-right: 5px;
    }


    .nomination-add-input, .team-add-input {
        display: block;
        width: 97%;
        height: 30px;
        padding: 0.375rem 0.75rem;
        margin-top: 5px;
        margin-bottom: 5px;
        margin-right: 10px;
        margin-left: 0;
        font-family: inherit;
        font-size: 16px;
        font-weight: 400;
        line-height: 2;
        color: #212529;
        background-color: #fff;
        background-clip: padding-box;
        border: 1px solid #9f9f9f;
        border-radius: 0.25rem;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }

    .nomination-add-input::placeholder, .team-add-input::placeholder {
        color: #212529;
        opacity: 0.4;
    }


    .delete-nomination-button, .delete-team-button {
        background-color: #b24848;
        font-weight: 400;
        color: white;
        border: 1px solid #962c2c;
        border-radius: 5px;
    }

    .team-list-div, .team-add-input-div {
        margin-left: 30px;
    }
</style>

<script>
    function displayDetails()
    {
        var elem = document.getElementById('documentorderwork-supplement-compliance_document').getElementsByTagName('input');
        var details = document.getElementById('details');

        if (elem[0].checked)
            details.style.display = "none";
        else
            details.style.display = "block";

        let item = [1, 2, 3];
        item.forEach((element) => {
            if (elem[element].checked)
                details.childNodes[2*element-1].hidden = false;
            else
                details.childNodes[2*element-1].hidden = true;
        });
    }

    let listId = 'nomDdList'; //айди выпадающего списка, в который будут добавлены номинации
    let listId2 = 'teamDdList'; //айди выпадающего списка, в который будут добавлены команды

    let nominations = [];
    let team = [];

    window.onload = function(){
        let noms = document.getElementById("prev-nom").innerHTML;

        if (noms.length > 5)
        {
            nominations = noms.split("%boobs%");

            nominations.pop();
            //--Костыль, почему-то в первую строку приходит перенос строки и несколько пробелов--
            //nominations[0] = nominations[0].substring(5);
            //-----------------------------------------------------------------------------------
            FinishNom();
        }

        let teams = document.getElementById("prev-team").innerHTML;
        if (teams.length > 5)
        {
            team = teams.split("%boobs%");

            team.pop();
            //--Костыль, почему-то в первую строку приходит перенос строки и несколько пробелов--
            //team[0] = team[0].substring(5);
            //-----------------------------------------------------------------------------------
            FinishTeam();
        }

        if (document.getElementById('documentorderwork-order_date').value === '')
        {
            document.getElementById('documentorderwork-supplement-foreign_event_goals_id').childNodes[0].childNodes[0].checked = true;
            document.getElementById('documentorderwork-supplement-compliance_document').childNodes[0].childNodes[0].checked = true;
        }
        document.getElementsByClassName('form-group field-documentorderwork-foreign_event-is_minpros')[0].childNodes[4].style.color = 'white';
        displayDetails();
    }

    function AddElem(list_row, list_item, arr, list_name)
    {
        let item = document.getElementsByClassName(list_row)[0];
        let itemCopy = item.cloneNode(true)
        itemCopy.getElementsByClassName(list_item)[0].innerHTML = '<p>' + arr[i] + '</p>'
        itemCopy.style.display = 'block';

        let list = document.getElementById(list_name);
        list.append(itemCopy);
    }

    function AddNom()
    {
        let elem = document.getElementById('nom-name');
        elem.value = elem.value.replace(/ +/g, ' ').trim();

        if (elem.value !== '' && nominations.indexOf(elem.value) === -1)
        {
            nominations.push(elem.value);

            let item = document.getElementsByClassName('nomination-list-row')[0];
            let itemCopy = item.cloneNode(true)
            itemCopy.getElementsByClassName('nomination-list-item')[0].innerHTML = '<p>' + elem.value + '</p>'
            itemCopy.style.display = 'block';

            let list = document.getElementById('list');
            list.append(itemCopy);

            elem.value = '';
        }
        else
            alert('Вы ввели пустые или повторные данные!');

        FinishNom();
    }

    function AddTeam()
    {
        let elem = document.getElementById('team-name');
        elem.value = elem.value.replace(/ +/g, ' ').trim();

        if (elem.value !== '' && team.indexOf(elem.value) === -1)
        {
            team.push(elem.value);

            let item = document.getElementsByClassName('team-list-row')[0];
            let itemCopy = item.cloneNode(true)
            itemCopy.getElementsByClassName('team-list-item')[0].innerHTML = '<p>' + elem.value + '</p>'
            itemCopy.style.display = 'block';

            let list = document.getElementById('list2');
            list.append(itemCopy);

            elem.value = '';
        }
        else
            alert('Вы ввели пустые или повторные данные!');

        FinishTeam();
    }

    function DelNom(elem)
    {
        let orig = elem.parentNode.parentNode;

        let name = elem.parentNode.parentNode.getElementsByClassName('nomination-list-item')[0].childNodes[0].textContent;
        nominations.splice(nominations.indexOf(name), 1);
        elem.parentNode.parentNode.parentNode.removeChild(orig);

        FinishNom();
    }

    function DelTeam(elem)
    {
        let orig = elem.parentNode.parentNode;

        let name = elem.parentNode.parentNode.getElementsByClassName('team-list-item')[0].childNodes[0].textContent;
        team.splice(team.indexOf(name), 1);
        elem.parentNode.parentNode.parentNode.removeChild(orig);

        FinishTeam();
    }

    function FinishNom()
    {
        let elem = document.getElementsByClassName(listId);

        for (let z = 0; z < elem.length; z++)
        {
            while (elem[z].options.length) {
                elem[z].options[0] = null;
            }

            elem[z].appendChild(new Option("--", 'NULL'));

            for (let i = 0; i < nominations.length; i++)
            {
                var option = document.createElement('option');
                option.value = nominations[i];
                option.innerHTML = nominations[i];
                elem[z].appendChild(option);
            }
        }
    }

    function FinishTeam()
    {
        let elem = document.getElementsByClassName(listId2);

        for (let z = 0; z < elem.length; z++)
        {
            while (elem[z].options.length) {
                elem[z].options[0] = null;
            }

            elem[z].appendChild(new Option("--", 'NULL'));

            for (let i = 0; i < team.length; i++)
            {
                var option = document.createElement('option');
                option.value = team[i];
                option.innerHTML = team[i];
                elem[z].appendChild(option);
            }
        }
    }

    function NextStep()
    {
        let foreign = document.getElementById('foreign-block');
        let nom = document.getElementById('nom-team-block');
        let btn = document.getElementById('nextBtn');

        foreign.disabled = !foreign.disabled;
        nom.disabled = !nom.disabled;
        if (foreign.disabled === false)
        {
            foreign.style.filter = 'blur(0px)';
            nom.style.filter = 'blur(1px)';
            btn.innerHTML = 'Вернуться к заполнению номинаций и команд';
        }
        else
        {
            nom.style.filter = 'blur(0px)';
            foreign.style.filter = 'blur(1px)';
            btn.innerHTML = 'Перейти к заполнению участников мероприятия';
        }
    }

    function NewPart()
    {
        let nom = document.getElementsByClassName(listId);
        let teams = document.getElementsByClassName(listId2);
        let item = teams.length - 1;    // добавляем только новым участникам команды и номинации

        while (teams[item].options.length) {
            teams[item].options[0] = null;
        }

        while (nom[item].options.length) {
            nom[item].options[0] = null;
        }

        teams[item].appendChild(new Option("--", 'NULL'));
        nom[item].appendChild(new Option("--", 'NULL'));

        for (let i = 0; i < team.length; i++)
        {
            var option = document.createElement('option');
            option.value = team[i];
            option.innerHTML = team[i];
            teams[item].appendChild(option);
        }

        for (let i = 0; i < nominations.length; i++)
        {
            var option = document.createElement('option');
            option.value = nominations[i];
            option.innerHTML = nominations[i];
            nom[item].appendChild(option);
        }
    }

    function ClickBranch(elem, index)
    {
        if (index === 4)
        {
            let parent = elem.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;
            let childs = parent.querySelectorAll('.col-xs-4');
            let first_gen = childs[1].querySelectorAll('.form-group');
            let second_gen = first_gen[3].querySelectorAll('.form-control');
            if (second_gen[0].hasAttribute('disabled'))
                second_gen[0].removeAttribute('disabled');
            else
            {
                second_gen[0].value = 1;
                second_gen[0].setAttribute('disabled', 'disabled');
            }
        }
    }
</script>

<div class="document-order-form">

    <?php
    $model->people_arr = \app\models\work\PeopleWork::find()->select(['id as value', "CONCAT(secondname, ' ', firstname, ' ', patronymic) as label"])->asArray()->all();
    $form = ActiveForm::begin(['id' => 'dynamic-form']);
    ?>

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

    <?php $model->nomenclature_id = '5'; ?>

    <?php
    $params = [
        //'prompt' => '',
        'id' => 'rS',
        'class' => 'form-control nom',
    ];
    $noms = \app\models\work\NomenclatureWork::find()->where(['branch_id' => $model->nomenclature_id])->andWhere(['actuality' => 0])->all();
    $items = \yii\helpers\ArrayHelper::map($noms,'number','fullNameWork');
    echo '<div id="order-number-1">';
    echo $form->field($model, 'order_number')->dropDownList($items, $params)->label('Код и описание номенклатуры');
    echo '</div>';
    ?>

    <div id="foreign-event-form" style="display: block;">
        <div class="row" style="overflow-y: scroll; height: 100%">
            <div class="panel panel-default">
                <div class="panel-heading"><h4><i class="glyphicon glyphicon-tag"></i> Информация для создания карточки учета достижений</h4></div>
                <div style="padding: 15px;">

                    <?= $form->field($model, "foreign_event[name]")->label('Название мероприятия') ?>

                    <?php
                    $company = \app\models\work\CompanyWork::find()->orderBy(['name' => SORT_ASC])->all();
                    $items = \yii\helpers\ArrayHelper::map($company,'id','name');
                    $params = [
                        'prompt' => '--',
                    ];
                    echo $form->field($model, "foreign_event[company_id]")->dropDownList($items,$params)->label('Организатор');
                    ?>

                    <?= $form->field($model, "foreign_event[start_date]")->widget(\yii\jui\DatePicker::class,
                        ([
                            'dateFormat' => 'php:Y-m-d',
                            'language' => 'ru',
                            'options' => [
                                'placeholder' => 'Дата начала мероприятия',
                                'class'=> 'form-control',
                                'autocomplete'=>'off',
                            ],
                            'clientOptions' => [
                                'changeMonth' => true,
                                'changeYear' => true,
                                'yearRange' => '2000:2050',
                            ]
                        ]))->label('Дата начала') ?>

                    <?= $form->field($model, "foreign_event[finish_date]")->widget(\yii\jui\DatePicker::class,
                        ([
                            'dateFormat' => 'php:Y-m-d',
                            'language' => 'ru',
                            'options' => [
                                'placeholder' => 'Дата окончания мероприятия',
                                'class'=> 'form-control',
                                'autocomplete'=>'off',
                            ],
                            'clientOptions' => [
                                'changeMonth' => true,
                                'changeYear' => true,
                                'yearRange' => '2000:2050',
                            ]]))->label('Дата окончания') ?>

                    <?= $form->field($model, "foreign_event[city]")->textInput(['maxlength' => true])->label('Город') ?>

                    <?php
                    $ways = \app\models\work\EventWayWork::find()->orderBy(['name' => SORT_ASC])->all();
                    $items = \yii\helpers\ArrayHelper::map($ways,'id','name');
                    $params = [
                    ];
                    echo $form->field($model, "foreign_event[event_way_id]")->dropDownList($items,$params)->label('Формат проведения');
                    ?>

                    <?php
                    $levels = \app\models\work\EventLevelWork::find()->orderBy(['name' => SORT_ASC])->all();
                    $items = \yii\helpers\ArrayHelper::map($levels,'id','name');
                    $params = [
                    ];
                    echo $form->field($model, "foreign_event[event_level_id]")->dropDownList($items,$params)->label('Уровень');
                    ?>

                    <?= $form->field($model, 'foreign_event[is_minpros]')->checkbox()->label('Входит в перечень Минпросвещения РФ'); ?>

                    <?= $form->field($model, "foreign_event[min_participants_age]")->textInput()->label('Мин. возраст участников (лет)') ?>

                    <?= $form->field($model, "foreign_event[max_participants_age]")->textInput()->label('Макс. возраст участников (лет)') ?>

                    <?= $form->field($model, "foreign_event[key_words]")->textInput(['maxlength' => true])->label('Ключевые слова') ?>

                </div>
            </div>
        </div>
    </div>

    <?php
    if ($model->id != NULL)
        echo $form->field($model, 'order_name')->textInput(['maxlength' => true, 'readonly' => true])->label('Наименование приказа');
    else
        echo $form->field($model, 'order_name')->textInput(['maxlength' => true, 'readonly' => true, 'value' => 'Об участии в мероприятии'])->label('Наименование приказа');
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

    <div id="supplement" style="display: block;">
        <div class="row" style="overflow-y: scroll; height: 100%">
            <div class="panel panel-default">
                <div class="panel-heading"><h4><i class="glyphicon glyphicon-tag"></i> Дополнительная информация для генерации приказа</h4></div>
                <div style="padding: 15px;">
                    <?php
                    $goals = \app\models\work\ForeignEventGoalsWork::find()->all();
                    $items = \yii\helpers\ArrayHelper::map($goals,'id','name');
                    $params = [
                        'prompt' => '--',
                    ];
                    echo $form->field($model, "supplement[foreign_event_goals_id]")->radioList($items,$params)->label('Уставная цель'); ?>

                    <?php
                    $params = [
                        '0' => 'Отсутствует',
                        '1' => 'Регламент',
                        '2' => 'Письмо',
                        '3' => 'Положение',
                    ];
                    echo $form->field($model, "supplement[compliance_document]")->radioList($params, ['onchange' => "displayDetails()"])->label('Документ о мероприятии');
                    ?>

                    <div id="details" style="display: <?= ($model->supplement->compliance_document === '0' || $model->supplement->compliance_document === null) ? 'none' : 'block'?>">
                        <p hidden style="color: red;">Ожидаемый формат описания регламента: "<i>соревнований за Кубок России по судомодельному спорту в классах радиоуправляемых яхт</i>"</p>
                        <p hidden style="color: red;">Ожидаемый формат описания письма: "<i>от 04.05.2023 г. № 02-02/201 «О проведении конкурса»</i>"</p>
                        <p hidden style="color: red;">Ожидаемый формат описания положения: "<i>об открытом чемпионате России 2024 г. по волейболу</i>"</p>
                        <?= $form->field($model, "supplement[document_details]")->textInput()->label('Описание документа для вставки в приказ')?>
                    </div>

                    <?php
                    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
                    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                    $params = [
                        'prompt' => '',
                    ];
                    echo $form->field($model, "supplement[collector_id]")->dropDownList($items,$params)->label('Ответственный за сбор и предоставление информации');
                    ?>

                    <?= $form->field($model, "supplement[information_deadline]")->textInput(['type' => 'number', 'min' => '1'])->label('Срок предоставления информации (в днях)')?>

                    <?php
                    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
                    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                    $params = [
                        'prompt' => '',
                    ];
                    echo $form->field($model, "supplement[contributor_id]")->dropDownList($items,$params)->label('Ответственный за внесение в ЦСХД');
                    ?>

                    <?= $form->field($model, "supplement[input_deadline]")->textInput(['type' => 'number', 'min' => '1'])->label('Срок внесения информации (в днях)')?>

                    <?php
                    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
                    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                    $params = [
                        'prompt' => '',
                    ];

                    echo $form->field($model, "supplement[methodologist_id]")->dropDownList($items,$params)->label('Ответственный за методологический контроль');

                    echo $form->field($model, "supplement[informant_id]")->dropDownList($items,$params)->label('Ответственный за информирование работников');
                    ?>

                </div>
            </div>
        </div>
    </div>

<?php /*------------------------------------*/?>

    <div id="prev-nom" style="display: none"><?php
        $noms = TeacherParticipantWork::find()->where(['foreign_event_id' => $model->foreign_event->id])->all();
        $nomsArr = [];
        foreach ($noms as $nom)
            if (!in_array($nom->nomination, $nomsArr) && $nom->nomination != null)
                $nomsArr[] = $nom->nomination;

        $nominations = $nomsArr;
        if ($nominations !== null && count($nominations))
            foreach ($nominations as $nomination)
                echo $nomination.'%boobs%';
        ?></div>

    <div id="prev-team" style="display: none"><?php
        $teams = TeamNameWork::find()->where(['foreign_event_id' => $model->foreign_event->id])->all();
        $teamArr = [];
        foreach ($teams as $team)
            if (!in_array($team->name, $teamArr))
                $teamArr[] = $team->name;

        $teams = $teamArr;
        if ($teams !== null && count($teams))
            foreach ($teams as $team)
                echo $team.'%boobs%';
        ?></div>

    <fieldset id="nom-team-block">
    <div class="main-div">
        <div class="nomination-div">
            <div class="nomination-heading"><h4><i class="glyphicon glyphicon-tower"></i>Номинации и команды</h4></div>
            <div class="nomination-add-div">
                <div class="nomination-add-input-div">
                    <label class="nomination-label-input">Номинация
                        <input class="nomination-add-input" id="nom-name" placeholder="Введите номинацию" type="text"/>
                    </label>
                </div>
                <div class="nomination-add-button-div">
                    <button type="button" onclick="AddNom()" class="nomination-add-button btn btn-success">Добавить<br>номинацию</button>
                </div>
                <div class="team-add-input-div">
                    <label class="team-label-input">Команда
                        <input class="team-add-input" id="team-name" placeholder="Введите название команды" type="text"/>
                    </label>
                </div>
                <div class="team-add-button-div">
                    <button type="button" onclick="AddTeam()" class="team-add-button btn btn-success">Добавить<br>команду</button>
                </div>
            </div>

            <div style="display: flex;">
                <div id="list" class="nomination-list-div">
                <?php

                $flag = count($nominations) > 0;
                $strDisplay = $flag ? 'block' : 'none';

                ?>
                <div class="nomination-list-row" style="display: none">
                    <div class="nomination-list-item-delete">
                        <button type="button" onclick="DelNom(this)" class="delete-nomination-button">X</button>
                    </div>
                    <div class="nomination-list-item">
                        <p>DEFAULT_ITEM</p>
                    </div>
                </div>

                <?php

                if ($flag)
                    foreach ($nominations as $nomination)
                        echo '<div class="nomination-list-row" style="display: block">
                                <div class="nomination-list-item-delete">
                                    <button type="button" onclick="DelNom(this)" class="delete-nomination-button">X</button>
                                </div>
                                <div class="nomination-list-item"><p>'.$nomination.'</p></div>
                            </div>';?>
            </div>

                <div id="list2" class="team-list-div">
                <?php

                $flag2 = count($nominations) > 0;
                $strDisplay2 = $flag2 ? 'block' : 'none';

                ?>
                <div class="team-list-row" style="display: none">
                    <div class="team-list-item-delete">
                        <button type="button" onclick="DelTeam(this)" class="delete-team-button">X</button>
                    </div>
                    <div class="team-list-item">
                        <p>DEFAULT_ITEM</p>
                    </div>
                </div>

                <?php

                if ($flag2)
                    foreach ($teams as $team)
                        echo '<div class="team-list-row" style="display: block">
                                <div class="team-list-item-delete">
                                    <button type="button" onclick="DelTeam(this)" class="delete-team-button">X</button>
                                </div>
                                <div class="team-list-item"><p>'.$team.'</p></div>
                            </div>';?>
            </div>
            </div>
        </div>
    </div>
    </fieldset>

    <div style="margin: 5%; text-align: center;">
        <button id="nextBtn" type="button" onclick="NextStep()" class="btn btn-info">Перейти к заполнению участников мероприятия</button>
    </div>

    <fieldset id="foreign-block" style="filter: blur(1px);" disabled>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-user"></i>Акты участия</h4></div>
            <?php
            $forEvent = \app\models\work\ForeignEventWork::find()->where($model->id !== null ? ['order_participation_id' => $model->id] : '0')->one();
            if ($forEvent !== null)
                $parts = \app\models\work\TeacherParticipantWork::find()->where(['foreign_event_id' => $forEvent->id])->all();
            else
                $parts = null;

            $editIcon = '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:1em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512"><path fill="currentColor" d="M498 142l-46 46c-5 5-13 5-17 0L324 77c-5-5-5-12 0-17l46-46c19-19 49-19 68 0l60 60c19 19 19 49 0 68zm-214-42L22 362 0 484c-3 16 12 30 28 28l122-22 262-262c5-5 5-13 0-17L301 100c-4-5-12-5-17 0zM124 340c-5-6-5-14 0-20l154-154c6-5 14-5 20 0s5 14 0 20L144 340c-6 5-14 5-20 0zm-36 84h48v36l-64 12-32-31 12-65h36v48z"></path></svg>';
            $deleleIcon = '<svg aria-hidden="true" style="display:inline-block;font-size:inherit;height:1em;overflow:visible;vertical-align:-.125em;width:.875em" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512"><path fill="currentColor" d="M32 464a48 48 0 0048 48h288a48 48 0 0048-48V128H32zm272-256a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zm-96 0a16 16 0 0132 0v224a16 16 0 01-32 0zM432 32H312l-9-19a24 24 0 00-22-13H167a24 24 0 00-22 13l-9 19H16A16 16 0 000 48v32a16 16 0 0016 16h416a16 16 0 0016-16V48a16 16 0 00-16-16z"></path></svg>';
            if ($parts !== null && count($parts) > 0)
            {
                echo '<table class="table table-bordered">';
                echo '<tr>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Участник</b></h4></td>
                            <td style="border-bottom: 2px solid black; "><h4><b>Отдел(-ы)</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Педагог</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Направленность</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Номинация</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Команда</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Форма реализации</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b>Материалы</b></h4></td>
                            <td style="padding-left: 20px; border-bottom: 2px solid black"><h4><b></b></h4></td>
                       </tr>';
                foreach ($parts as $partOne) {
                    $partOnePeople = \app\models\work\ForeignEventParticipantsWork::find()->where(['id' => $partOne->participant_id])->one();
                    $partFiles = \app\models\work\ParticipantFilesWork::find()->where(['teacher_participant_id' => $partOne->id])->one();
                    //var_dump($partFiles);
                    $partOneTeacher = \app\models\work\PeopleWork::find()->where(['id' => $partOne->teacher_id])->one();
                    $partTwoTeacher = \app\models\work\PeopleWork::find()->where(['id' => $partOne->teacher2_id])->one();
                    $teachersStr = '';
                    if ($partOneTeacher !== null) $teachersStr .= $partOneTeacher->shortName;
                    if ($partTwoTeacher !== null) $teachersStr .= '<br>'.$partTwoTeacher->shortName;
                    $team = \app\models\work\TeamWork::find()->where(['teacher_participant_id' => $partOne->id])->one();
                    $realizes = \app\models\work\AllowRemoteWork::find()->where(['id' => $partOne->allow_remote_id])->one();
                    echo '<tr><td style="padding-left: 20px">'. $partOnePeople->shortName.'&nbsp;</label>'.'</td>'.
                        '<td style="padding-left: 20px">'. $partOne->getBranchsString().'&nbsp;</label>'.'</td>'.
                        '<td style="padding-left: 20px">'.$teachersStr.'</td>'.
                        '<td style="padding-left: 10px">'.$partOne->focus0->name.'</td>'.
                        '<td style="padding-left: 10px">'. $partOne->nomination .'</td>'.
                        '<td style="padding-left: 10px">'.$team->teamNameWork->name.'</td>'.
                        '<td style="padding-left: 10px">'.$realizes->name.'</td>';
                    if ($partFiles == null)
                        echo '<td style="padding-left: 10px; text-align: center;"> -- </td>';
                    else
                        echo '<td style="padding-left: 10px; text-align: center;">'.Html::a('📁↓', \yii\helpers\Url::to(['document-order/get-file', 'fileName' => $partFiles->filename, 'type' => 'participants', 'event' => true])).'</td>';
                    echo '<td style="padding-left: 10px">'.
                        Html::a($editIcon, \yii\helpers\Url::to(['document-order/update-participant', 'id' => $partOne->id, 'model_id' => $model->id])). ' ' .
                        Html::a($deleleIcon, \yii\helpers\Url::to(['document-order/delete-participant', 'id' => $partOne->id, 'model_id' => $model->id])).
                        '</td></tr>';
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
                    <?php foreach ($modelParticipants as $i => $modelParticipantsOne):
                        ?>
                        <div class="item panel panel-default" style="padding: 0;"><!-- widgetBody -->
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

                                    $people = \app\models\work\ForeignEventParticipantsWork::find()->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
                                    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                    $params = [
                                        'prompt' => ''
                                    ];
                                    echo $form->field($modelParticipantsOne, "[{$i}]fio")->dropDownList($items,$params)->label('ФИО участника');

                                    $branchs = \app\models\work\BranchWork::find()->where(['!=', 'id', '5'])->orderBy(['id' => SORT_ASC])->all();
                                    $items = \yii\helpers\ArrayHelper::map($branchs, 'id', 'name');
                                    echo '<div class="'.$i.'">';
                                    echo $form->field($modelParticipantsOne, "[{$i}]branch[]")->checkboxList(
                                        $items, ['item' => function ($index, $label, $name, $checked, $value) {
                                        return
                                            '<div class="checkbox" style="font-size: 16px; font-family: Arial; color: black;">
                                                        <label for="branch-'. $index .'">
                                                            <input onclick="ClickBranch(this, '.$index.')" class="check_branch" name="'. $name .'" type="checkbox" '. $checked .' value="'. $value .'">
                                                            '. $label .'
                                                        </label>
                                                    </div>';
                                    }, 'class' => 'base'])->label('<u>Отдел(-ы)</u>');
                                    echo '</div>';
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div>
                                    <?php
                                    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->orderBy(['secondname' => SORT_ASC, 'firstname' => SORT_ASC])->all();
                                    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
                                    $params = [
                                        'prompt' => ''
                                    ];
                                    echo $form->field($modelParticipantsOne, "[{$i}]teacher")->dropDownList($items,$params)->label('ФИО педагогов');
                                    echo $form->field($modelParticipantsOne, "[{$i}]teacher2")->dropDownList($items,$params)->label(false);
                                    $focuses = \app\models\work\FocusWork::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($focuses,'id','name');
                                    $params = [
                                        'prompt' => ''
                                    ];
                                    echo $form->field($modelParticipantsOne, "[{$i}]focus")->dropDownList($items,$params)->label('Направленность');
                                    $realizes = \app\models\work\AllowRemoteWork::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($realizes,'id','name');
                                    $params = [
                                        //'prompt' => ''
                                        'disabled' => true,
                                    ];
                                    echo $form->field($modelParticipantsOne, "[{$i}]allow_remote_id")->dropDownList($items,$params)->label('Форма реализации');
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div style="margin-bottom: 27px;">
                                    <?= $form->field($modelParticipantsOne, "[{$i}]file")->fileInput()->label('Представленные материалы') ?>
                                    <?php
                                    if ($model->id !== null)
                                        $files = \app\models\work\ParticipantFilesWork::find()->joinWith(['teacherParticipant teacherParticipant'])->where(['teacherParticipant.foreign_event_id' => $model->foreign_event->id])->all();
                                    else
                                        $files = \app\models\work\ParticipantFilesWork::find()->all();
                                    $items = \yii\helpers\ArrayHelper::map($files,'filename','filename');
                                    $params = [
                                        'prompt' => ''
                                    ];
                                    echo $form->field($modelParticipantsOne, "[{$i}]file")->dropDownList($items,$params)->label(false);
                                    ?>
                                </div>
                            </div>
                            <div class="col-xs-4">
                                <div>
                                    <div class="form-group field-foreigneventparticipantsextended-0-team has-success">
                                        <label class="control-label" for="foreigneventparticipantsextended-0-team">В составе команды</label>
                                        <select id="ddListTeam" class="form-control teamDdList" name="ForeignEventParticipantsExtended[<?= $i ?>][team]">
                                            <option>--</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-xs-4">
                                <div>
                                    <div class="form-group field-foreigneventparticipantsextended-0-nominations has-success">
                                        <label class="control-label">Номинация</label>
                                        <select id="ddList" class="form-control nomDdList" name="ForeignEventParticipantsExtended[<?= $i ?>][nomination]">
                                            <option>--</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body" style="padding: 0; margin: 0"></div>

                        </div>
                    <?php
                    endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>
    </fieldset>
<?php /*------------------------------------*/?>

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
        <?php
        if ($model->id == null)
            echo Html::submitButton('Сохранить приказ', ['value' => 'create?modelType=2', 'class' => 'btn btn-success']);
        else
            echo Html::submitButton('Сохранить приказ', ['class' => 'btn btn-success']);
        ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$js =<<< JS
    $(".dynamicform_wrapper").on("afterInsert", function(e, item) {
        NewPart();

        let elems = document.getElementsByClassName('base');
        
        let values = [];
        for (let i = 0; i < elems[0].children.length; i++)
            if (elems[1].children[i].childElementCount > 0)
                values[i] = elems[0].children[i].children[0].children[0].value;
        for (let j = 1; j < elems.length; j++)
            for (let i = 0; i < elems[1].children.length; i++)
                if (elems[j].children[i].childElementCount > 0)
                   elems[j].children[i].children[0].children[0].value = values[i]; 


    });

JS;
$this->registerJs($js, \yii\web\View::POS_LOAD);
?>
