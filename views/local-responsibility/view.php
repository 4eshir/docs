<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\LocalResponsibility */

$this->title = $model->people->secondname.' '.$model->responsibilityType->name;
$this->params['breadcrumbs'][] = ['label' => 'Учет ответственности работников', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="local-responsibility-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Вы действительно хотите удалить данную ответственность?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'responsibilityTypeStr', 'format' => 'raw'],
            ['attribute' => 'branchStr', 'format' => 'raw'],
            ['attribute' => 'auditoriumStr', 'format' => 'raw'],
            ['attribute' => 'peopleStr', 'format' => 'raw'],
            ['attribute' => 'regulationStr', 'format' => 'raw'],
            ['label' => 'Файлы', 'attribute' => 'files', 'value' => function ($model) {
                $split = explode(" ", $model->files);
                $result = '';
                for ($i = 0; $i < count($split) - 1; $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['local-responsibility/get-file', 'fileName' => $split[$i]])).'<br>';
                return $result;
            }, 'format' => 'raw'],
        ],
    ]) ?>

</div>