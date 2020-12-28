<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\common\Regulation */

$this->title = 'Добавить положение';
$session = Yii::$app->session;
$this->params['breadcrumbs'][] = ['label' => $session->get('type') == 1 ? 'Положение об учебном процессе' : 'Положение о мероприятиях',
                                  'url' => ['index', 'c' => $session->get('type')]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="regulation-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelExpire' => $modelExpire,
    ]) ?>

</div>
