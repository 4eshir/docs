<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\Container */

$this->title = 'Добавить контейнер';
$this->params['breadcrumbs'][] = ['label' => 'Контейнеры', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelObject' => $modelObject,
    ]) ?>

</div>
