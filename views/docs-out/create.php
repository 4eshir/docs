<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\DocumentOutWork */

$this->title = 'Добавить исходящий документ';
$this->params['breadcrumbs'][] = ['label' => 'Исходящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-out-create">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
