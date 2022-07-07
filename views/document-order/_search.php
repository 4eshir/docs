<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SearchDocumentOrder */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="document-order-search">

    <?php
    $session = Yii::$app->session;
    $form = ActiveForm::begin([
        'action' => ['index', 'c' => $session->get('type')],
        'method' => 'get',
    ]); ?>
    <?php
    $branchs = \app\models\work\BranchWork::find()->all();
    $items = \yii\helpers\ArrayHelper::map($branchs,'id','name');
    $params = [
        'prompt' => '',
    ];

    if ($session->get('type') != 1)
        echo $form->field($model, 'nomenclature_id')->dropDownList($items,$params)->label('Отдел');

    ?>

    <?= $form->field($model, 'key_words')->label('Поиск по ключевым словам') ?>
    <div class="form-group">
        <?= Html::submitButton('Найти', ['class' => 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
