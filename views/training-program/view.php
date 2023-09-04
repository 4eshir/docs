<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\TrainingProgramWork */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Образовательные программы', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="training-program-view">

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

    .hoverless:hover {
        cursor: default;
    }
</style>

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить программу?',
                'method' => 'post',
            ],
        ]) ?>
        <?php
            $error = $model->getErrorsWork();
            if ($error !== '' && (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 6) || (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7))))
                echo Html::a('Простить ошибки', ['amnesty', 'id' => $model->id], ['class' => 'btn btn-warning',
                        'data' => [
                            'confirm' => 'Вы действительно хотите простить все ошибки в данной образовательной программе?',
                            'method' => 'post',
                        ],]);
        ?>
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
            'name',
            ['attribute' => 'level', 'value' => function ($model) {return $model->level+1;}],
            'ped_council_date',
            'ped_council_number',
            ['attribute' => 'compilers', 'format' => 'html'],
            'capacity',
            'student_left_age',
            'student_right_age',
            'stringFocus',
            ['attribute' => 'trueName', 'label' => 'Тематическое направление', 'value' => function($model) {return $model->thematicDirection->full_name . ' (' . $model->thematicDirection->name . ')';}],
            'hour_capacity',
            ['attribute' => 'themesPlan', 'value' => '<button class="accordion">Показать учебно-тематический план</button><div class="panel">'.$model->themesPlan.'</div>', 'format' => 'raw', 'format' => 'raw', 'label' => 'Учебно-тематический план'],
            ['attribute' => 'branchs', 'format' => 'raw'],
            ['attribute' => 'allowRemote', 'format' => 'raw'],
            /*['attribute' => 'allow_remote', 'label' => 'Форма реализации', 'value' => function($model) {
                $out = '';
                if ($model->allow_remote == 0) $out = 'Только очная форма';
                if ($model->allow_remote == 1) $out = 'Очная форма, с применением дистанционных технологий';
                return $out;}],*/
            ['attribute' => 'doc_file', 'value' => function ($model) {
                return Html::a($model->doc_file, \yii\helpers\Url::to(['training-program/get-file', 'fileName' => $model->doc_file, 'modelId' => $model->id, 'type' => 'doc']));
            }, 'format' => 'raw'],
            ['attribute' => 'edit_docs', 'value' => function ($model) {
                $split = explode(" ", $model->edit_docs);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['training-program/get-file', 'fileName' => $split[$i], 'modelId' => $model->id, 'type' => 'edit_docs'])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['attribute' => 'certificatTypeString', 'label' => 'Итоговая форма контроля'],
            ['attribute' => 'description', 'label' => 'Описание'],
            'key_words',
            ['attribute' => function($model) {return $model->actual == 0 ? 'Нет' : 'Да';}, 'label' => 'Образовательная программа актуальна'],
            ['attribute' => 'linkGroups', 'value' => '<div style="float: left; width: 20%; height: 100%; line-height: 250%">'.$model->getGroupsCount().'</div><div style="float: left; width: 80%"><button class="accordion" style="display: flex; float: left">Показать учебные группы</button><div class="panel">'.$model->getLinkGroups().'</div></div>', 'format' => 'raw', 'label' => 'Учебные группы'],
            ['attribute' => 'creatorString', 'format' => 'raw'],
            ['attribute' => 'lastUpdateString', 'format' => 'raw'],
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