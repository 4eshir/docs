<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\DocumentInWork */

$this->title = 'Редактировать входящий документ: ' . $model->document_theme;
$this->params['breadcrumbs'][] = ['label' => 'Входящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->document_theme, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактировать';
?>
<div class="document-in-update">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
