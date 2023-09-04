<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\MaterialObject */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Материальные ценности', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
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
        margin-bottom: 0;
    }

    .hoverless:hover {
        cursor: default;
    }
</style>

<div class="material-object-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?php /* Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить объект?',
                'method' => 'post',
            ],
        ])*/ ?>
        <?php
        $error = $model->getErrorsWork();
        if ($error !== '' && (\app\models\components\RoleBaseAccess::CheckRole(Yii::$app->user->identity->getId(), 7)))
            echo Html::a('Простить ошибки', ['amnesty', 'id' => $model->id], ['class' => 'btn btn-warning',
                'data' => [
                    'confirm' => 'Вы действительно хотите простить все ошибки?',
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
            'photo_local',
            'photo_cloud',
            //'count',
            'priceString',
            ['attribute' => 'numberLink', 'format' => 'raw'],
            'attribute',
            'financeSourceString',
            'inventory_number',
            'isEducationString',
            ['attribute' => 'kindString', /*'value' => '<div style="float: left; width: 20%; height: 100%; line-height: 250%">'.$model->kindWork->name.'</div><div style="float: left; width: 80%"><button class="accordion" style="display: flex; float: left">Показать характеристики</button><div class="panel">'.$model->getKindString().'</div></div>',*/ 'format' => 'raw'],
            'typeString',
            'state',
            'damage',
            'statusString',
            'writeOffString',
            'lifetime',
            'expiration_date',
            'create_date',
        ],
    ]) ?>

    <div <?php echo $model->complex == 1 ? '' : 'hidden'; ?>>
        <h4><u>Компоненты объекта</u></h4>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                ['attribute' => 'complexString', 'format' => 'raw'],
            ],
        ]) ?>
    </div>

    <div>
        <h4><u>Контейнеры</u></h4>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                ['attribute' => 'atContainerLink', 'format' => 'raw'],
                ['attribute' => 'inContainerLink', 'format' => 'raw'],
            ],
        ]) ?>
    </div>

    <div>
        <h4><u>Материальная ответственность</u></h4>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                ['attribute' => 'MOL', 'format' => 'raw'],
            ],
        ]) ?>
    </div>
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