<?php

use yii\helpers\Html;



?>

<h3>Отчеты по готовым формам</h3>

<?php
echo Html::a("Эффективный контракт", \yii\helpers\Url::to(['report-form/effective-contract']), ['class'=>'btn btn-success']);
//echo Html::a("Эффективный контракт", \yii\helpers\Url::to(['report-form/effective-contract']), ['class'=>'btn btn-success']);
?>