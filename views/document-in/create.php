<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\DocumentInWork */

$this->title = 'Добавить входящий документ';
$this->params['breadcrumbs'][] = ['label' => 'Входящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-in-create">

    <h3><?= Html::encode($this->title) ?></h3>
    <br>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
