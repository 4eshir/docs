<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\CertificatTemplatesWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="certificat-templates-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>


    <?= $form->field($model, 'templateFile')->fileInput() ?>

    <?php
        if ($model->path !== null)
        {
            echo '<table>';
            echo '<tr><td><h5>Загруженный файл: '
                    .Html::a($model->path, \yii\helpers\Url::to(['certificat-templates/get-file', 'fileName' => $model->path, 'modelId' => $model->id]))
                    .'</h5></td><td style="padding-left: 10px">'
                    //.Html::a('X', \yii\helpers\Url::to(['certificat-templates/delete-file', 'modelId' => $model->id]))
                    .'</td></tr>';
            echo '</table>';
        }
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
