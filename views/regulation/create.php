<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\Regulation */

$this->title = 'Добавить положение';
$this->params['breadcrumbs'][] = ['label' => 'Положения', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="regulation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelExpire' => $modelExpire,
    ]) ?>

</div>
