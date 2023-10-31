<?php

use yii\helpers\Html;


?>

<?php
$this->title = 'Отчеты по готовым формам';
?>

<h3>Отчеты по готовым формам</h3>

<?php
echo Html::a("Эффективный контракт", \yii\helpers\Url::to(['report-form/effective-contract']), ['class'=>'btn btn-success']);
echo '<div style="padding-top: 7px"></div>';
echo Html::a("Отчет ДОД", \yii\helpers\Url::to(['report-form/dod']), ['class'=>'btn btn-success']);
echo '<div style="padding-top: 7px"></div>';
echo Html::a("Отчет 1-ДОП", \yii\helpers\Url::to(['report-form/do-dop-1']), ['class'=>'btn btn-success']);
echo '<div style="padding-top: 7px"></div>';
echo Html::a("Отчет гос. задание", \yii\helpers\Url::to(['report-form/gz']), ['class'=>'btn btn-success']);
echo '<div style="padding-top: 7px"></div>';
echo Html::a("Отчет ДО", \yii\helpers\Url::to(['report-form/do']), ['class'=>'btn btn-success']);
echo '<div style="padding-top: 7px"></div>';
echo Html::a("Расчет выработки пед. работников", \yii\helpers\Url::to(['report-form/teacher']), ['class'=>'btn btn-success']);


echo '<div style="padding-top: 50px"></div>';
echo Html::a("Отчет гос. задание 2.0", \yii\helpers\Url::to(['report-form/gz-2']), ['class'=>'btn btn-primary']);
echo '<div style="padding-top: 7px"></div>';
echo Html::a("Отчет ДОД 2.0", \yii\helpers\Url::to(['report-form/dod-2']), ['class'=>'btn btn-primary']);
echo '<div style="padding-top: 7px"></div>';

?>




<?php /*echo Html::a('<img class="left" width="30px"/> Распечатать .PDF', ['/report-form/mpdf-blog'], [
                                'class'=>'btn btn-default',
                                'target'=>'_blank', 
                                'data-toggle'=>'tooltip', 
                                'title'=>'Will open the generated PDF file in a new window'
                            ]);*/?>