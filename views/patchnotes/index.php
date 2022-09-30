<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\PatchnotesWork */
?>

<style>
    .accordion {
        background-color: #3680b1;
        color: white;
        cursor: pointer;
        padding: 8px;
        width: 100%;
        text-align: left;
        border: none;
        outline: none;
        transition: 0.4s;
        border-radius: 5px;
    }

    /* Add a background color to the button if it is clicked on (add the .active class with JS), and when you move the mouse over it (hover) */
    .active, .accordion:hover {

    }

    .info2 {
        background-color: rgba(248, 248, 165, 0.5);
        border-radius: 10px;
        font-size: 18px;
        font-family: "Times New Roman";
        padding: 5px 5px 5px 20px;
        width: 500px;
        margin-bottom: 10px;
        margin-top: 10px;
        margin-left: 50px;


    }

    .info {
        background-color: rgba(141, 224, 141, 0.5);
        border-radius: 10px;
        font-size: 18px;
        font-family: "Times New Roman";
        padding: 5px 5px 5px 20px;
        width: 500px;
        margin-bottom: 10px;
    }

    .flexdiv {
        display: flex;
        flex-direction: row;
    }
</style>

<?php
$notes = \app\models\work\PatchnotesWork::find()->orderBy(['date' => SORT_DESC])->all();
?>

<h3><b>Последнее обновление</b></h3>
<div class="flexdiv">
    <div>
        <div class="info">
            <?php
            $noteArr = explode("\r\n", $notes[0]->text);
            echo '<h4><u>Обновление '.$notes[0]->first_number.'.'.$notes[0]->second_number.' от '.$notes[0]->date.'</u></h4>';
            echo '<ul>';
            foreach ($noteArr as $str) {
                echo '<li>'.$str.'</li>';
            }
            echo '</ul>';
            ?>
        </div>
        <button class="accordion" style="max-width: 500px" onclick="showNotes()">Показать предыдущие обновления</button>
    </div>
    <div class="panel" hidden style="overflow-y; scroll; height: calc(25% - 20px)">
        <?php
        for ($i = 1; $i < count($notes); $i++)
        {
            echo '<div class="info2">';
            $noteArr = explode("\r\n", $notes[$i]->text);
            echo '<h4><u>Обновление '.$notes[$i]->first_number.'.'.$notes[$i]->second_number.' от '.$notes[$i]->date.'</u></h4>';
            echo '<ul>';
            foreach ($noteArr as $str) {
                echo '<li>'.$str.'</li>';
            }
            echo '</ul>';
            echo '</div>';
        }
        ?>
    </div>
</div>



<script>
    function showNotes()
    {
        var elem = document.getElementsByClassName("panel");
        if (elem[0].hidden === true)
        {
            elem[0].removeAttribute('hidden');
            var button = document.getElementsByClassName("accordion");
            button[0].innerHTML = "Скрыть предыдущие обновления";
            button[0].style.backgroundColor = "rgb(255,255,193)";
            button[0].style.color = "black";
        }
        else
        {
            elem[0].setAttribute('hidden', 'true');
            button = document.getElementsByClassName("accordion");
            button[0].innerHTML = "Показать предыдущие обновления";
            button[0].style.backgroundColor = "#3680b1";
            button[0].style.color = "white";
        }
    }
</script>