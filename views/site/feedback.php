<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\common\Feedback */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = 'Форма обратной связи';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="alert alert-warning">
    <?php $message = 'Система находится в стадии тестирования. Пожалуйста, опишите Вашу проблему как можно более подробно. По возможности опишите выполняемые действия. 
    Эл. почта для экстренной связи или пересылки файлов <b>gkalashnik@schooltech.ru</b>.'; ?>
    <?= nl2br(Html::decode($message)) ?>
</div>
<div class="site-login">
    <h1><?= Html::encode($this->title) ?></h1>



    <div class="row">
        <div class="col-lg-5">
            <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

            <?= $form->field($model, 'text')->textarea(['autofocus' => true, 'rows' => '10'])->label('Описание проблемы') ?>

            <div class="form-group">
                <?= Html::submitButton('Отправить', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
            </div>

            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>