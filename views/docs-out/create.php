<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentOut */

$this->title = 'Добавить исходящий документ';
$this->params['breadcrumbs'][] = ['label' => 'Исходящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-out-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
