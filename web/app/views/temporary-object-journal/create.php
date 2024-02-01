<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\TemporaryObjectJournal */

$this->title = 'Create Temporary Object Journal';
$this->params['breadcrumbs'][] = ['label' => 'Temporary Object Journals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temporary-object-journal-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
