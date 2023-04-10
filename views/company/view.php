<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\CompanyWork */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Организации', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="company-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить данную организацию?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['label' => 'Тип организации', 'attribute' => 'company_type', 'value' => function($model){
                return $model->companyType->type;
            }],
            ['label' => 'Название организации', 'attribute' => 'name'],
            ['label' => 'Краткое название', 'attribute' => 'short_name'],
        ],
    ]) ?>

    <?php
    if ($model->is_contractor)
    {
        echo '<h4><u>Информация по контрагенту</u></h4>';

        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                ['label' => 'ИНН организации', 'attribute' => 'inn'],
                ['label' => 'Категория СМСП', 'attribute' => 'categorySmspString'],
                ['label' => 'Комментарий', 'attribute' => 'comment'],
                'phone_number',
                'email',
                'site',
            ],
        ]);
    }
    ?>

</div>
