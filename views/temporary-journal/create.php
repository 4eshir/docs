<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\TemporaryJournal */

$this->title = 'Create Temporary Journal';
$this->params['breadcrumbs'][] = ['label' => 'Temporary Journals', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="temporary-journal-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
