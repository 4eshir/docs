<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\EventFormWork */

$this->title = 'Добавить форму мероприятий';
$this->params['breadcrumbs'][] = ['label' => 'Формы мероприятий', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="event-form-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
