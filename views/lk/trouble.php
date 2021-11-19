<?php

use app\models\work\ErrorsWork;
use app\models\work\UserWork;

/* @var $this yii\web\View */
/* @var $model app\models\work\LocalResponsibilityWork */
/* @var $dataProvider yii\data\ActiveDataProvider */

//$this->title = $model->people->secondname.' '.$model->responsibilityType->name;
?>

<?php
$access = [12, 13, 14];
$isMethodist = \app\models\common\AccessLevel::find()->where(['user_id' => Yii::$app->user->identity->getId()])->andWhere(['in', 'access_id', $access])->one();
?>

<div style="width:100%; height:1px; clear:both;"></div>
<div>
    <?= $this->render('menu') ?>

    <?php /*echo '<b>Фильтры для учебных групп: </b>';

    echo '<input type="text" id="nameSearch" onkeydown="return preventEnter(event.key)" onchange="searchColumn()" placeholder="Поиск по части имени..." title="Введите имя">';
    echo '    С <input type="date" id="nameLeftDate" onkeydown="return preventEnter(event.key)" onchange="searchColumn()" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="Поиск по дате начала занятий...">';
    echo '    По <input type="date" id="nameRightDate" onkeydown="return preventEnter(event.key)" onchange="searchColumn()" pattern="[0-9]{4}-[0-9]{2}-[0-9]{2}" placeholder="Поиск по дате начала занятий...">';
    */?>

    <div class="content-container col-xs-8" style="float: left">
        <?php
            $user = UserWork::find()->where(['id' => Yii::$app->user->identity->getId()])->one();
            $erros = new ErrorsWork();
            echo $erros->ErrorsElectronicJournalSubsystem($user, 0);    // если второй параметр 0, то выводим все ошибки, если 1, то только критические
        ?>
    </div>
    <div>
        <div class="" data-html="true" style="position: fixed; z-index: 101; width: 30px; height: 30px; padding: 5px 0 0 0; background: #09ab3f; color: white; text-align: center; display: inline-block; border-radius: 4px;" title="Белый цвет - обычная ошибка&#10Желтый цвет - критическая ошибка">❔</div>
    </div>
</div>
<div style="width:100%; height:1px; clear:both;"></div>

<script>
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