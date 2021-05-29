<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\ResponsibilityType */

$this->title = 'Create Responsibility Type';
$this->params['breadcrumbs'][] = ['label' => 'Responsibility Types', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="responsibility-type-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
