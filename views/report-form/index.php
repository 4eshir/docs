<?php

use yii\helpers\Html;



?>

<h3>Отчеты по готовым формам</h3>

<?php
echo Html::a("Эффективный контракт", \yii\helpers\Url::to(['report-form/effective-contract']), ['class'=>'btn btn-success']);
echo '<div style="padding-top: 7px"></div>';
echo Html::a("Отчет 1-ДОП", \yii\helpers\Url::to(['report-form/do-dop-1']), ['class'=>'btn btn-success']);
echo '<div style="padding-top: 7px"></div>';
echo Html::a("Отчет гос. задание", \yii\helpers\Url::to(['report-form/gz']), ['class'=>'btn btn-success']);
echo '<div style="padding-top: 7px"></div>';
echo Html::a("Отчет ДО", \yii\helpers\Url::to(['report-form/do']), ['class'=>'btn btn-success']);
?>