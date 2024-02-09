<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\HistoryObject */

$this->title = 'Create History Object';
$this->params['breadcrumbs'][] = ['label' => 'History Objects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="history-object-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
