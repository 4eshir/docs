<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\Regulation */

$this->title = 'Редактировать положение: ' . $model->name;
$session = Yii::$app->session;
$tmp = \app\models\common\RegulationType::find()->where(['id' => $session->get('type')])->one()->name;

$this->params['breadcrumbs'][] = ['label' => $tmp, 'url' => ['index', 'c' => $session->get('type')]];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="regulation-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelExpire' => $modelExpire,
    ]) ?>

</div>
