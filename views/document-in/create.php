<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\DocumentIn */

$this->title = 'Добавить входящий документ';
$this->params['breadcrumbs'][] = ['label' => 'Входящая документация', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="document-in-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
