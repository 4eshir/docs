<?php

use app\models\common\AsInstall;
use app\models\common\UseYears;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\AsAdmin */

$this->title = 'Добавить ПО';
$this->params['breadcrumbs'][] = ['label' => 'ПО "Административный процесс"', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="as-admin-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelAsInstall' => (empty($modelAsInstall)) ? [new AsInstall] : $modelAsInstall,
    ]) ?>

</div>
