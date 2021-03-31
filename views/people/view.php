<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\People */

$this->title = $model->secondname.' '.$model->firstname.' '.$model->patronymic;
$this->params['breadcrumbs'][] = ['label' => 'Люди', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="people-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы уверены, что хотите удалить этого человека?',
                'method' => 'post',
            ],
        ]) ?>
    </p>
    <h4><u>Общая информация</u></h4>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['label' => 'Фамилия', 'attribute' => 'secondname'],
            ['label' => 'Имя', 'attribute' => 'firstname'],
            ['label' => 'Отчество', 'attribute' => 'patronymic'],
            ['label' => 'Должность', 'attribute' => 'position', 'value' => function($model){
                return $model->position->name;
            }],
            ['label' => 'Организация', 'attribute' => 'company', 'value' => function($model){
                return $model->company->name;
            }],

            ['label' => 'Уникальный идентификатор', 'attribute' => 'short', 'format' => 'raw', 'visible' => $model->short !== null && $model->short !== ''],
            ['label' => 'Отдел по трудовому договору', 'attribute' => 'branch_id', 'format' => 'raw', 'value' => function($model) {return Html::a($model->branch->name, \yii\helpers\Url::to(['branch/view', 'id' => $model->branch_id]));}, 'visible' => $model->branch_id !== null],
        ],
    ]) ?>
    <?php
    $vis = strlen($model->groupsList) > 3 ? 'visible' : 'hidden';
    echo "<div style='visibility: ".$vis."'>";
    ?>
        <h4><u>Информация об образовательной деятельности</u></h4>
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                ['label' => 'Группы', 'attribute' => 'groupsList', 'format' => 'raw'],
                ['label' => 'Достижения учеников', 'attribute' => 'achievements', 'format' => 'raw'],

            ],
        ]) ?>
    </div>


</div>
