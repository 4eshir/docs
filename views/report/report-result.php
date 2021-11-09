<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\extended\ResultReportModel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="result-report-form">
    <div style="font-family: Tahoma; font-size: 20px">
        <?php echo $model->result; ?>
        <?php echo $model->debugInfo; ?>
    </div>
</div>
