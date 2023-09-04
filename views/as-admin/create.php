<?php

use app\models\work\AsInstallWork;
use app\models\work\UseYearsWork;
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\work\AsAdminWork */

$this->title = 'Добавить ПО';
$this->params['breadcrumbs'][] = ['label' => 'ПО "Административный процесс"', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="as-admin-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelAsInstall' => (empty($modelAsInstall)) ? [new AsInstallWork] : $modelAsInstall,
    ]) ?>

</div>
