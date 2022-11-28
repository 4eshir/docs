<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\BotMessage */

$this->title = 'Create Bot Message';
$this->params['breadcrumbs'][] = ['label' => 'Bot Messages', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="bot-message-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
