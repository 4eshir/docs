<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\extended\ResultReportModel */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
$this->title = 'Отчет';
?>


<div class="result-report-form">
    <div style="font-family: Tahoma; font-size: 20px">
        <?php echo $model->result; ?>
        <?php
        $session = Yii::$app->session;
        $session->set('csv', $model->debugInfo2);
        if (strlen($model->debugInfo) > 100)
            echo Html::a('Скачать подробный отчет по обучающимся', \yii\helpers\Url::to(['report/get-full-report'])); ?>
        <?php
        if (strlen($model->debugInfo) > 300)
            echo $model->debugInfo; ?>
    </div>
</div>
