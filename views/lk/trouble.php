<?php

use app\models\work\ErrorsWork;
use app\models\work\UserWork;

/* @var $this yii\web\View */
/* @var $model app\models\work\LocalResponsibilityWork */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = $model->people->secondname.' '.$model->responsibilityType->name;
?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <?= $this->render('menu') ?>

    <?php echo '<b style="padding: 50px;">Поиск в таблице: </b>';

    echo '<input style="width: 400px;" type="text" id="nameSearch" onchange="searchColumn()" placeholder="Введите код проблемы, описание или место возникновения" title="Введите имя">';
    ?>

    <div class="content-container col-xs-8" style="float: left; padding-top: 30px;">
        <?php
            $user = UserWork::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
            $errors = new ErrorsWork();

            //echo $errors->ErrorsElectronicJournalSubsystem($user, 0);    // если второй параметр 0, то выводим все ошибки, если 1, то только критические
            echo $errors->ErrorsSystem($user, 0);    // если второй параметр 0, то выводим все ошибки, если 1, то только критические

        ?>
    </div>
    <div>
        <div class="" data-html="true" style="position: fixed; z-index: 101; width: 30px; height: 30px; padding: 5px 0 0 0; background: #09ab3f; color: white; text-align: center; display: inline-block; border-radius: 4px;" title="Белый цвет - обычная ошибка&#10Желтый цвет - критическая ошибка">❔</div>
    </div>
</div>
<div style="width:100%; height:1px; clear:both;"></div>

<script>
    window.onload = function() {
        initData();
    }

    const initData = () => {
        tableGr = document.getElementById('training-group');
        if (tableGr !== null)
        {
            headersGr = tableGr.querySelectorAll('th');
            tableBodyGr = tableGr.querySelector('tbody');
            rowsGr = tableBodyGr.querySelectorAll('tr');
        }

        tablePr = document.getElementById('training-program');
        if (tablePr !== null)
        {
            headersPr = tablePr.querySelectorAll('th');
            tableBodyPr = tablePr.querySelector('tbody');
            rowsPr = tableBodyPr.querySelectorAll('tr');
        }

        tableDocOrd = document.getElementById('document-order');
        if (tableDocOrd !== null)
        {
            headersDocOrd = tableDocOrd.querySelectorAll('th');
            tableBodyDocOrd = tableDocOrd.querySelector('tbody');
            rowsDocOrd = tableBodyDocOrd.querySelectorAll('tr');
        }

        tableEvent = document.getElementById('event');
        if (tableEvent !== null)
        {
            headersEvent = tableEvent.querySelectorAll('th');
            tableBodyEvent = tableEvent.querySelector('tbody');
            rowsEvent = tableBodyEvent.querySelectorAll('tr');
        }

        tableForeignEvent = document.getElementById('foreignEvent');
        if (tableForeignEvent !== null)
        {
            headersForeignEvent = tableForeignEvent.querySelectorAll('th');
            tableBodyForeignEvent = tableForeignEvent.querySelector('tbody');
            rowsForeignEvent = tableBodyForeignEvent.querySelectorAll('tr');
        }

        // Направление сортировки
        directionsGr = Array.from(headersGr).map(function(header) {
            return '';
        });

        directionsPr = Array.from(headersPr).map(function(header) {
            return '';
        });

        directionsDocOrd = Array.from(headersDocOrd).map(function(header) {
            return '';
        });

        directionsEvent = Array.from(headersEvent).map(function(header) {
            return '';
        });

        directionsForeignEvent = Array.from(headersForeignEvent).map(function(header) {
            return '';
        });
    }

    let tableGr = '';
    let tablePr = '';
    let tableDocOrd = '';
    let tableEvent = '';
    let tableForeignEvent = '';

    let headersGr = '';
    let headersPr = '';
    let headersDocOrd = '';
    let headersEvent = '';
    let headersForeignEvent = '';

    let tableBodyGr = '';
    let tableBodyPr = '';
    let tableBodyDocOrd = '';
    let tableBodyEvent = '';
    let tableBodyForeignEvent = '';

    let rowsGr = '';
    let rowsPr = '';
    let rowsDocOrd = '';
    let rowsEvent = '';
    let rowsForeignEvent = '';

    let directionsGr = '';
    let directionsPr = '';
    let directionsDocOrd = '';
    let directionsEvent = '';
    let directionsForeignEvent = '';

    function fFor(rows, filterName) {
        for (let i = 0; i < rows.length; i++)
        {
            let td = rows[i].getElementsByTagName("td");
            let tdCode = td[0];
            let tdName = td[1];
            let tdPlace = td[2];
            let tdBranch = td[3];

            if (td) {
                let txtValueName = tdName.textContent || tdName.innerText;
                let txtValueCode = tdCode.textContent || tdCode.innerText;
                let txtValuePlace = tdPlace.textContent || tdPlace.innerText;
                let txtValueBranch = tdBranch.textContent || tdBranch.innerText;

                if (txtValueName.toUpperCase().indexOf(filterName) > -1 || txtValueCode.toUpperCase().indexOf(filterName) > -1 || txtValuePlace.toUpperCase().indexOf(filterName) > -1 || txtValueBranch.toUpperCase().indexOf(filterName) > -1)
                    rows[i].style.display = "";
                else
                    rows[i].style.display = "none";
            }
        }
    }

    function searchColumn() {
        var inputName, filterName;

        inputName = document.getElementById('nameSearch');
        filterName = inputName.value.toUpperCase();

        fFor(rowsGr, filterName);
        fFor(rowsPr, filterName);
        fFor(rowsDocOrd, filterName);
        fFor(rowsEvent, filterName);
        fFor(rowsForeignEvent, filterName);
    }

    function sortColumn(index) {
        // Получить текущее направление
        const directionGr = directionsGr[index] || 'asc';
        const directionPr = directionsPr[index] || 'asc';
        const directionDocOrd = directionsDocOrd[index] || 'asc';
        const directionEvent = directionsEvent[index] || 'asc';
        const directionForeignEvent = directionsForeignEvent[index] || 'asc';

        // Фактор по направлению
        const multiplierGr = (directionGr === 'asc') ? 1 : -1;
        const multiplierPr = (directionPr === 'asc') ? 1 : -1;
        const multiplierDocOrd = (directionDocOrd === 'asc') ? 1 : -1;
        const multiplierEvent = (directionEvent === 'asc') ? 1 : -1;
        const multiplierForeignEvent = (directionForeignEvent === 'asc') ? 1 : -1;

        const newRowsGr = Array.from(rowsGr);
        const newRowsPr = Array.from(rowsPr);
        const newRowsDocOrd = Array.from(rowsDocOrd);
        const newRowsEvent = Array.from(rowsEvent);
        const newRowsForeignEvent = Array.from(rowsForeignEvent);


        newRowsGr.sort(function(rowA, rowB) {
            const cellA = rowA.querySelectorAll('td')[index].innerHTML;
            const cellB = rowB.querySelectorAll('td')[index].innerHTML;

            switch (true) {
                case cellA > cellB: return 1 * multiplierGr;
                case cellA < cellB: return -1 * multiplierGr;
                case cellA === cellB: return 0;
            }
        });
        newRowsPr.sort(function(rowA, rowB) {
            const cellA = rowA.querySelectorAll('td')[index].innerHTML;
            const cellB = rowB.querySelectorAll('td')[index].innerHTML;

            switch (true) {
                case cellA > cellB: return 1 * multiplierPr;
                case cellA < cellB: return -1 * multiplierPr;
                case cellA === cellB: return 0;
            }
        });
        newRowsDocOrd.sort(function(rowA, rowB) {
            const cellA = rowA.querySelectorAll('td')[index].innerHTML;
            const cellB = rowB.querySelectorAll('td')[index].innerHTML;

            switch (true) {
                case cellA > cellB: return 1 * multiplierDocOrd;
                case cellA < cellB: return -1 * multiplierDocOrd;
                case cellA === cellB: return 0;
            }
        });
        newRowsEvent.sort(function(rowA, rowB) {
            const cellA = rowA.querySelectorAll('td')[index].innerHTML;
            const cellB = rowB.querySelectorAll('td')[index].innerHTML;

            switch (true) {
                case cellA > cellB: return 1 * multiplierEvent;
                case cellA < cellB: return -1 * multiplierEvent;
                case cellA === cellB: return 0;
            }
        });
        newRowsForeignEvent.sort(function(rowA, rowB) {
            const cellA = rowA.querySelectorAll('td')[index].innerHTML;
            const cellB = rowB.querySelectorAll('td')[index].innerHTML;

            switch (true) {
                case cellA > cellB: return 1 * multiplierForeignEvent;
                case cellA < cellB: return -1 * multiplierForeignEvent;
                case cellA === cellB: return 0;
            }
        });


        // Удалить старые строки
        [].forEach.call(rowsGr, function(row) {
            tableBodyGr.removeChild(row);
        });
        [].forEach.call(rowsPr, function(row) {
            tableBodyPr.removeChild(row);
        });
        [].forEach.call(rowsDocOrd, function(row) {
            tableBodyDocOrd.removeChild(row);
        });
        [].forEach.call(rowsEvent, function(row) {
            tableBodyEvent.removeChild(row);
        });
        [].forEach.call(rowsForeignEvent, function(row) {
            tableBodyForeignEvent.removeChild(row);
        });


        // Поменять направление
        directionsGr[index] = directionGr === 'asc' ? 'desc' : 'asc';
        directionsPr[index] = directionPr === 'asc' ? 'desc' : 'asc';
        directionsDocOrd[index] = directionDocOrd === 'asc' ? 'desc' : 'asc';
        directionsEvent[index] = directionEvent === 'asc' ? 'desc' : 'asc';
        directionsForeignEvent[index] = directionForeignEvent === 'asc' ? 'desc' : 'asc';


        // Добавить новую строку
        newRowsGr.forEach(function(newRow) {
            tableBodyGr.appendChild(newRow);
        });
        newRowsPr.forEach(function(newRow) {
            tableBodyPr.appendChild(newRow);
        });
        newRowsDocOrd.forEach(function(newRow) {
            tableBodyDocOrd.appendChild(newRow);
        });
        newRowsEvent.forEach(function(newRow) {
            tableBodyEvent.appendChild(newRow);
        });
        newRowsForeignEvent.forEach(function(newRow) {
            tableBodyForeignEvent.appendChild(newRow);
        });
    }

    function hide(index) {
        if (index === 0)
            if (tableGr.style.display === "block")
                tableGr.style.display = "none";
            else
                tableGr.style.display = "block";

        if (index === 1)
            if (tablePr.style.display === "block")
                tablePr.style.display = "none";
            else
                tablePr.style.display = "block";

        if (index === 2)
            if (tableDocOrd.style.display === "block")
                tableDocOrd.style.display = "none";
            else
                tableDocOrd.style.display = "block";

        if (index === 3)
            if (tableEvent.style.display === "block")
                tableEvent.style.display = "none";
            else
                tableEvent.style.display = "block";

        if (index === 4)
            if (tableForeignEvent.style.display === "block")
                tableForeignEvent.style.display = "none";
            else
                tableForeignEvent.style.display = "block";
    }
</script>