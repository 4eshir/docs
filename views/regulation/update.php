<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\Regulation */

$this->title = 'Редактировать положение: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Положения', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="regulation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelExpire' => $modelExpire,
    ]) ?>

</div>
