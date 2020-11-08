<?php

use yii\helpers\Html;
use app\models\common\AsInstall;

/* @var $this yii\web\View */
/* @var $model app\models\common\AsAdmin */

$this->title = 'Редактировать: ' . $model->as_name;
$this->params['breadcrumbs'][] = ['label' => 'As Admins', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="as-admin-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelAsInstall' => (empty($modelAsInstall)) ? [new AsInstall] : $modelAsInstall,
    ]) ?>

</div>
