<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\common\MaterialObject */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Редактировать объект: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Материальные ценности', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="material-object-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php /*$this->render('_form', [
        'model' => $model,
    ])*/ ?>

    <?php $form = ActiveForm::begin(); ?>

    <div id="inventory_number" style="display: <?php echo $model->inventory_number != '' ? 'block' : 'none';?>">
        <?php echo '<h2> Инвентарный номер: '.$model->inventory_number.'</h2>'; ?>
    </div>

    <?= $form->field($model, 'photoFile')->fileInput(['multiple' => false]) ?>

    <?= $form->field($model, 'is_education')->checkbox() ?>

    <?= $form->field($model, 'damage')->textarea(['rows' => '5']) ?>

    <div id="state-div" style="display: <?php echo $model->type == 2 ? 'block' : 'none'; ?>">
        <?= $form->field($model, 'state')->textInput(['type' => 'number', 'style' => 'width: 30%']) ?>
    </div>

    <?= $form->field($model, 'status')->checkbox(); ?>

    <?php
    $items = [0 => 'Списание не требуется', 1 => 'Готов к списанию', 2 => 'Списан'];
    $params = [
        'style' => 'width: 30%'
    ];
    echo $form->field($model, 'write_off')->dropDownList($items,$params);

    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
