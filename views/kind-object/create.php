<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\KindObject */

$this->title = 'Create Kind Object';
$this->params['breadcrumbs'][] = ['label' => 'Kind Objects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="kind-object-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelCharacteristics' => $modelCharacteristics,
    ]) ?>

</div>
