<?php

use app\models\work\TrainingGroupParticipantWork;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\TrainingGroupWork */

$this->title = $model->number;
$this->params['breadcrumbs'][] = ['label' => 'Учебные группы', 'url' => ['index']];
$this->params['breadcrumbs'][] = 'Группа '.$this->title;
\yii\web\YiiAsset::register($this);
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

    /* Style the accordion panel. Note: hidden by default */
    .panel {
        padding: 0 18px;
        background-color: white;
        display: none;
        overflow: hidden;
    }
</style>



<div class="training-group-view">

    <h1><?= Html::encode('Группа '.$this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить группу?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <div class="content-container" style="color: #ff0000; font: 18px bold;">
        <?php
            $error = $model->getErrorsWork();
            if ($error != '')
            {
                echo '<p style="">';
                echo $error;
                echo '</p>';
            }
        ?>
    </div>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'branchWork', 'label' => 'Отдел производящий учет', 'format' => 'html'],
            'number',
            ['attribute' => 'budgetText', 'label' => 'Форма обучения'],
            ['attribute' => 'programName', 'format' => 'html'],
            ['attribute' => 'teachersList', 'format' => 'html'],
            'start_date',
            'finish_date',
            ['attribute' => 'countParticipants', 'label' => 'Количество учеников', 'format' => 'html'],
            ['attribute' => 'participantNames', 'value' => '<button class="accordion">Показать состав группы</button><div class="panel">'.$model->participantNames.'</div>', 'format' => 'raw'],
            ['attribute' => 'countLessons', 'label' => 'Количество занятий в расписании', 'format' => 'html'],
            ['attribute' => 'lessonDates', 'value' => '<button class="accordion">Показать расписание группы</button><div class="panel">'.$model->lessonDates.'</div>', 'format' => 'raw'],
            ['attribute' => 'manHoursPercent', 'format' => 'raw', 'label' => 'Выработка человеко-часов'],
            ['attribute' => 'journalLink', 'format' => 'raw', 'label' => 'Журнал'],
            ['attribute' => 'ordersName', 'format' => 'html'],
            ['attribute' => 'photos', 'value' => function ($model) {
                $split = explode(" ", $model->photos);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['training-group/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'photos'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['attribute' => 'present_data', 'value' => function ($model) {
                $split = explode(" ", $model->present_data);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['training-group/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'present_data'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['attribute' => 'work_data', 'value' => function ($model) {
                $split = explode(" ", $model->work_data);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['training-group/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'work_data'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            'openText',
        ],
    ]) ?>

</div>


<script>
    var acc = document.getElementsByClassName("accordion");
    var i;

    for (i = 0; i < acc.length; i++) {
        acc[i].addEventListener("click", function() {
            /* Toggle between adding and removing the "active" class,
            to highlight the button that controls the panel */
            this.classList.toggle("active");

            /* Toggle between hiding and showing the active panel */
            var panel = this.nextElementSibling;
            if (panel.style.display === "block") {
                panel.style.display = "none";
            } else {
                panel.style.display = "block";
            }
        });
    }
</script>