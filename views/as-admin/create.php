<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\AsAdmin */

$this->title = 'Create As Admin';
$this->params['breadcrumbs'][] = ['label' => 'As Admins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="as-admin-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
