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
        <?php

        echo '<h3>'.$model->header.'</h3>';

        echo '<br>';

        echo $model->result;

        $session = Yii::$app->session;


        if (strlen($model->debugInfo2) > 150)
        {
            $session->set('csv1', $model->debugInfo2);
            echo Html::a('Скачать подробный отчет по обучающимся', \yii\helpers\Url::to(['report/get-full-report', 'type' => 1]));
        }
        echo '<br>';
        if (strlen($model->debugInfo) > 190)
        {
            $session->set('csv2', $model->debugInfo);
            echo Html::a('Скачать подробный отчет по человеко-часам', \yii\helpers\Url::to(['report/get-full-report', 'type' => 2]));
        }
        if ($model->debugInfo3 !== null)
        {
            $session->set('csv3', $model->debugInfo3);
            echo Html::a('Скачать подробный отчет по учету достижений в мероприятиях', \yii\helpers\Url::to(['report/get-full-report', 'type' => 3]));
        }
        ?>
    </div>
</div>
