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

        tablePart = document.getElementById('order_participant');
        tableBodyPart = tablePart.querySelector('tbody');
        rowsPart = tableBodyPart.querySelectorAll('tr');

        displayParticipant();
    }

    let table = '';
    let headers = '';
    let tableBody = '';
    let rows = '';
    let directions = '';
    let transform = '';

    let tablePart = '';
    let tableBodyPart = '';
    let rowsPart = '';

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

    function searchParticipant() {
        var inputName, filterName, tdName, txtValueName;

        inputName = document.getElementById('participantSearch');
        filterName = inputName.value.toUpperCase();

        for (let i = 0; i < rowsPart.length; i++)
        {
            tdName = rowsPart[i].getElementsByTagName("td")[1];

            if (tdName)
            {
                txtValueName = tdName.textContent || tdName.innerText;
                if (txtValueName.toUpperCase().indexOf(filterName) > -1)
                {
                    if (filterName !== "")
                        rowsPart[i].style.backgroundColor = "lightgreen";
                    else
                        rowsPart[i].style.backgroundColor = "";
                }
                else
                    rowsPart[i].style.backgroundColor = "";
            }
        }
    }

    function displayParticipant() {
        let nom = document.getElementById('rS').value;
        for (let i = 0; i < rowsPart.length; i++)
        {
            let tdPart = rowsPart[i].getElementsByTagName("td")[2].textContent;
            for (let j = 0; j < rows.length; j++)
            {
                let check = rows[j].getElementsByTagName("td")[0].querySelector('input').checked;
                let td = rows[j].getElementsByTagName("td")[1].textContent;
                if (check && td === tdPart)
                {
                    rowsPart[i].style.display = "";
                    break;
                }
                else
                {
                    rowsPart[i].style.display = "none";
                }
            }
        }

        if (nom === '11-31')
        {
            document.getElementById("order_participant").getElementsByTagName("thead")[0].getElementsByTagName("th")[3].style.display = "";
            for (let i = 0; i < rowsPart.length; i++)
            {
                if (rowsPart[i].style.display === "")
                    rowsPart[i].getElementsByTagName("td")[3].style.display = "";
            }
        }
        else
        {
            document.getElementById("order_participant").getElementsByTagName("thead")[0].getElementsByTagName("th")[3].style.display = "none";
            for (let i = 0; i < rowsPart.length; i++)
            {
                if (rowsPart[i].style.display === "")
                    rowsPart[i].getElementsByTagName("td")[3].style.display = "none";
            }
        }

        let date = document.getElementById("documentorderwork-order_date").value;
        document.getElementById("study_type-0").checked = false;
        for (let i = 0; i < rows.length; i++)
        {
            if (rows[i].getElementsByTagName("td")[0].querySelector('input').checked === true)
            {
                if (rows[i].getElementsByTagName("td")[3].textContent > date)
                {
                    //document.getElementById("study_type-0").checked = true;
                    break;
                }
            }
        }
    }

    function sortParticipant(index) {
        // Получить текущее направление
        const direction = directions[index] || 'asc';

        // Фактор по направлению
        const multiplier = (direction === 'asc') ? 1 : -1;

        const newRows = Array.from(rowsPart);

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
        [].forEach.call(rowsPart, function(row) {
            tableBodyPart.removeChild(row);
        });

        // Поменять направление
        directions[index] = direction === 'asc' ? 'desc' : 'asc';

        // Добавить новую строку
        newRows.forEach(function(newRow) {
            tableBodyPart.appendChild(newRow);
        });

    }

    function allCheck()
    {
        let elems = document.getElementsByClassName('check');
        for (var c = 0; c !== elems.length; c++)
        {
            if (elems[c].checked === false)
            {
                if (rowsPart[c].style.display !== "none")
                    elems[c].checked = true;
                else
                    elems[c].checked = false;
            }
            else
            {
                elems[c].checked = false;
            }
        }
    }

    function documentName()
    {
        let nom = document.getElementById('rS').value;
        if (nom === '09-01' || nom === '10-01' || nom === '11-01' || nom === '12-01' || nom === '13-01' || nom === '09-22' || nom === '10-26' || nom === '11-26')
        {
            document.getElementById('documentorderwork-order_name').value = 'О зачислении';
            document.getElementById('study-type').style.display = 'none';
            document.getElementById("study_type-0").checked = false;
        }
        if (nom === '09-02' || nom === '10-02' || nom === '11-02' || nom === '12-02' || nom === '13-02' || nom === '09-23' || nom === '10-27' || nom === '11-27')
        {
            document.getElementById('documentorderwork-order_name').value = 'Об отчислении';
            document.getElementById('study-type').style.display = '';
            document.getElementById('study-type').innerHTML = `<label class="control-label">Преамбула</label><div id="documentorderwork-study_type" role="radiogroup">
                <label class="modal-radio"><input type="radio" name="DocumentOrderWork[study_type]" value="0" tabindex="3" style="margin-right: 5px" ><i></i><span>По решению аттестационной комиссии/ протоколов жюри/ судейской коллегии/ итоговой диагностической карты</span></label><br>
                <label class="modal-radio"><input type="radio" name="DocumentOrderWork[study_type]" value="1" tabindex="3" style="margin-right: 5px" checked=""><i></i><span>В связи с завершением обучения без успешного прохождения итоговой формы контроля</span></label><br>
                <label class="modal-radio"><input type="radio" name="DocumentOrderWork[study_type]" value="2" tabindex="3" style="margin-right: 5px" ><i></i><span>По заявлению родителя</span></label><br>
                <label class="modal-radio"><input type="radio" name="DocumentOrderWork[study_type]" value="3" tabindex="3" style="margin-right: 5px" ><i></i><span>По соглашению сторон</span></label><br><br></div>`;
        }
        if (nom === '11-31')
        {
            document.getElementById('documentorderwork-order_name').value = 'О переводе';
            document.getElementById('study-type').style.display = '';
            document.getElementById('study-type').innerHTML = `<label class="control-label">Преамбула</label><div id="documentorderwork-study_type" role="radiogroup">
                <label class="modal-radio"><input type="radio" name="DocumentOrderWork[study_type]" value="0" tabindex="3" style="margin-right: 5px" ><i></i><span>На следующий год обучения</span></label><br>
                <label class="modal-radio"><input type="radio" name="DocumentOrderWork[study_type]" value="1" tabindex="3" style="margin-right: 5px" checked=""><i></i><span>С одной ДОП на другую ДОП</span></label><br>
                <label class="modal-radio"><input type="radio" name="DocumentOrderWork[study_type]" value="2" tabindex="3" style="margin-right: 5px" ><i></i><span>Из одной учебной группы в другую</span></label><br><br></div>`;
        }
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
        ]])->label('Дата приказа'); ?>


    <!---      -->
    <?php
    $params = [
        'prompt' => '--',
        'id' => 'r',
        'onchange' => '
                        $.post(
                            "' . Url::toRoute('subattr') . '", 
                            {id: $(this).val(),
                            date: document.getElementById("documentorderwork-order_date").value,
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
                                documentName();
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
        //'prompt' => '',
        'id' => 'rS',
        'class' => 'form-control nom',
        'onchange' => 'documentName()',
    ];

    echo '<div id="order-number-1">';
    if ($model->nomenclature_id === null)
        echo $form->field($model, 'order_number')->dropDownList([], $params)->label('Код и описание номенклатуры');
    else
    {
        $noms = \app\models\work\NomenclatureWork::find()->where(['branch_id' => $model->nomenclature_id])->andWhere(['actuality' => 0])->all();
        $items = \yii\helpers\ArrayHelper::map($noms,'number','fullNameWork');
        echo $form->field($model, 'order_number')->dropDownList($items, $params)->label('Код и описание номенклатуры');
    }
    echo '</div>';
    ?>

    <div id="study-type" style="display: block;">
        <?php
        $noms = NomenclatureWork::find()->where(['number' => $model->order_number])->andWhere(['actuality' => 0])->one();
        if ($model->id !== null && $noms->type != 0)
        {
            $radioArr = [0 => 'По решению аттестационной комиссии/ протоколов жюри/ судейской коллегии/ итоговой диагностической карты', 1 => 'В связи с завершением обучения без успешного прохождения итоговой формы контроля', 2 => 'По заявлению родителя', 3 => 'По соглашению сторон'];

            if ($noms->type == 2)
                $radioArr = [0 => 'На следующий год обучения', 1 => 'С одной ДОП на другую ДОП', 2 => 'Из одной учебной группы в другую'];
            echo $form->field($model, 'study_type')->radioList($radioArr,
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
                ])->label('Преамбула');
        }
        ?>
    </div>

    <div id="group_table" style="margin-bottom: 1em;">
        <?php
        echo '<b>Фильтры для учебных групп: </b>';

        echo '<input type="text" id="nameSearch" onkeydown="return preventEnter(event.key)" onchange="searchColumn()" placeholder="Поиск по части имени..." title="Введите имя">';
        echo '    С <input type="date" id="nameLeftDate" onkeydown="return preventEnter(event.key)" onchange="searchColumn()" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="Поиск по дате начала занятий...">';
        echo '    По <input type="date" id="nameRightDate" onkeydown="return preventEnter(event.key)" onchange="searchColumn()" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="Поиск по дате начала занятий...">';

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
                    echo '<input type="checkbox" checked="true" id="documentorderwork-groups_check" name="DocumentOrderWork[groups_check][]" onchange="displayParticipant()" value="'.$group->id.'">';
                else
                    echo '<input type="checkbox" id="documentorderwork-groups_check" name="DocumentOrderWork[groups_check][]" onchange="displayParticipant()" value="'.$group->id.'">';
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

            echo '<br><b>Учащиеся учебных групп: </b>';
            echo '<input type="text" id="participantSearch" onkeydown="return preventEnter(event.key)" onchange="searchParticipant()" placeholder="Поиск по учащимся..." title="Введите имя">';
            echo '<div style="max-height: 400px; overflow-y: scroll; margin-top: 1em;"><table id="order_participant" class="table table-bordered"><thead><tr><th><input type="checkbox" id="checker0" onclick="allCheck()"></th><th><a onclick="sortParticipant(1)"><b>Учащийся</b></a></th><th><a onclick="sortParticipant(2)"><b>Текущая учебная группа</b></a></th><th style="display: none;"><b>Новая учебная группа</b></th></tr></thead>';
            echo '';
            echo '<tbody>';
            $groupParticipants = \app\models\work\TrainingGroupParticipantWork::find()/*->where(['!=', 'status', 1])*/->andWhere(['IN', 'training_group_id',
                (new Query())->select('id')->from('training_group')->where(['order_stop' => 0])->andWhere(['archive' => 0])->andWhere(['branch_id' => $model->nomenclature_id])])->all();//->orderBy('training_group_id')->all();
            $part = \app\models\work\ForeignEventParticipantsWork::find();
            $stud = \app\models\work\TrainingGroupWork::find();
            foreach ($groupParticipants as $groupParticipant) {
                $ordersParticipant = \app\models\work\OrderGroupParticipantWork::find()->where(['group_participant_id' => $groupParticipant->id])->andWhere(['link_id' => NULL])->andWhere(['IN', 'order_group_id',
                    (new Query())->select('id')->from('order_group')->where(['document_order_id' => $model->id])])->all();
                if ($groups[0]->CheckParticipantStatus($groupParticipant) == 0 || count($ordersParticipant) !== 0)
                {
                    echo '<tr><td style="width: 10px">';
                    if (count($ordersParticipant) !== 0)
                        echo '<input type="checkbox" checked="true" id="documentorderwork-participants_check" name="DocumentOrderWork[participants_check][]" class="check" value="' . $groupParticipant->id . '">';
                    else
                        echo '<input type="checkbox" id="documentorderwork-participants_check" name="DocumentOrderWork[participants_check][]" class="check" value="' . $groupParticipant->id . '">';
                    echo '</td><td style="width: auto">';
                    echo $part->where(['id' => $groupParticipant->participant_id])->one()->getFullName();
                    echo '</td><td style="width: auto">';
                    $gr = $stud->where(['id' => $groupParticipant->training_group_id])->one();
                    echo $gr->number;
                    //{
                    echo '</td><td style="width: auto; display: none">';
                    $pastaAlDente = \app\models\work\OrderGroupParticipantWork::find()->where(['IS NOT', 'link_id', null])->andWhere(['IN', 'order_group_id',
                        (new Query())->select('id')->from('order_group')->where(['document_order_id' => $model->id])])->all();

                    //$newGroups = $stud->where(['training_program_id' => $gr->training_program_id])->andWhere(['!=', 'id', $gr->id])->andWhere(['>', 'finish_date', $model->order_date])->all();
                    $newGroups = $stud->where(['!=', 'id', $gr->id])->andWhere(['>', 'finish_date', $model->order_date])->andWhere(['branch_id' => $gr->branch_id])->andWhere(['archive' => 0])->all();
                    $items = \yii\helpers\ArrayHelper::map($newGroups, 'id', 'number');
                    $params = [];
                    if (count($ordersParticipant) !== 0) {
                        foreach ($pastaAlDente as $macaroni) {
                            if ($macaroni->groupParticipant->participant_id == $groupParticipant->participant_id) {
                                $params = [
                                    'value' => $macaroni->groupParticipant->training_group_id,
                                ];
                                break;
                            }
                        }
                    }


                    //echo $form->field($model, 'new_groups_check[]')->dropDownList($items, $params)->label(false);
                    echo $form->field($model, "new_groups_check[$groupParticipant->id][$groupParticipant->participant_id][]")->dropDownList($items, $params)->label(false);
                    //}
                    echo '</td></tr>';
                }
            }
            /*----------------*/
            if (\app\models\work\NomenclatureWork::find()->where(['number' => $model->order_number])->andWhere(['actuality' => 0])->one()->type === 1)
            {
                $pasta = \app\models\work\OrderGroupParticipantWork::find()->joinWith('orderGroup orderGroup')->where(['orderGroup.document_order_id' => $model->id])->all();
                $grPs = \app\models\work\TrainingGroupParticipantWork::find();
                foreach ($pasta as $macaroni)
                {
                    echo '<tr><td style="width: 10px">';
                    $grP = $grPs->where(['id' => $macaroni->group_participant_id])->one();
                    echo '<input type="checkbox" checked="true" id="documentorderwork-participants_check" name="DocumentOrderWork[participants_check][]" class="check" value="' . $grP->id . '">';
                    echo '</td><td style="width: auto">';
                    echo $part->where(['id' => $grP->participant_id])->one()->getFullName();
                    echo '</td><td style="width: auto">';
                    $gr = $stud->where(['id' => $grP->training_group_id])->one();
                    echo $gr->number;
                    echo '</td><td style="width: auto; display: none"></td></tr>';
                }
            }
            /*----------------*/
            echo '</tbody></table></div>';

        }

        ?>
    </div>

    <!---      -->

    <?php
    if ($model->id != NULL)
        echo $form->field($model, 'order_name')->textInput(['maxlength' => true, 'readonly' => true])->label('Наименование приказа');
    else
        echo $form->field($model, 'order_name')->textInput(['maxlength' => true, 'readonly' => true, 'value' => 'О зачислении'])->label('Наименование приказа');
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
