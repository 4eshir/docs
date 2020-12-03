<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchAsAdmin */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ПО "Административный процесс';
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    #content {
        width: 100%;
        overflow-x: scroll;
    }
    #container {
        height: 100px;
        width: 200px;
    }
    #topscrl  {
        height: 20px;
        width: 100%;
        overflow-x: scroll;
        display: none;
    }
    #topfake {
        height: 1px;
    }
</style>

<div class="as-admin-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить ПО', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div id="topscrl">
        <div id="topfake"></div>
    </div>
    <div style="overflow-x: scroll; Width:100%" id="content">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [

                ['attribute' => 'id', 'label' => '№ п/п'],
                ['attribute' => 'copyright_id', 'label' => 'Правообладатель', 'value' => 'copyright.name'],
                ['attribute' => 'as_name', 'label' => 'Наименование'],
                ['attribute' => 'requisites', 'label' => 'Реквизиты', 'value' => function($model){
                    if ($model->document_number == null)
                        return '';
                    return 'Компания: '.$model->asCompany->name.'<br>Номер док.: '.$model->document_number.'<br>Дата док.: '.$model->document_date;
                }, 'format' => 'raw'],
                ['attribute' => 'count', 'label' => 'Кол-во'],
                ['attribute' => 'price', 'label' => 'Стоимость'],
                ['attribute' => 'inst_quant', 'label' => 'Установ. Кванториум', 'value' => function($model){
                    $res = \app\models\common\AsInstall::find()->where(['as_admin_id' => $model->id])->andWhere(['branch_id' => 1])->all();
                    $html = '';
                    foreach ($res as $resOne)
                        $html = $html.'Кабинет: '.$resOne->cabinet.' ('.$resOne->count.' шт.)<br>';
                    return $html;
                }, 'format' => 'raw'],
                ['attribute' => 'inst_tech', 'label' => 'Установ. Технопарк', 'value' => function($model){
                    $res = \app\models\common\AsInstall::find()->where(['as_admin_id' => $model->id])->andWhere(['branch_id' => 2])->all();
                    $html = '';
                    foreach ($res as $resOne)
                        $html = $html.'Кабинет: '.$resOne->cabinet.' ('.$resOne->count.' шт.)<br>';
                    return $html;
                }, 'format' => 'raw'],
                ['attribute' => 'inst_cdntt', 'label' => 'Установ. ЦДНТТ', 'value' => function($model){
                    $res = \app\models\common\AsInstall::find()->where(['as_admin_id' => $model->id])->andWhere(['branch_id' => 3])->all();
                    $html = '';
                    foreach ($res as $resOne)
                        $html = $html.'Кабинет: '.$resOne->cabinet.' ('.$resOne->count.' шт.)<br>';
                    return $html;
                }, 'format' => 'raw'],
                ['attribute' => 'countryProd', 'label' => 'Страна производитель', 'value' => 'countryProd.name'],
                ['attribute' => 'useYear', 'label' => 'Годы использования', 'value' => function($model){
                    $res = \app\models\common\UseYears::find()->where(['as_admin_id' => $model->id])->one();
                    if ($res == null)
                        return '';
                    $html = '';
                    if ($res->start_date == '1999-01-01' && $res->end_date == '1999-01-01')
                        $html = 'Бессрочно';
                    else if ($res->end_date == '1999-01-01')
                        $html = $html.' '.$res->start_date.' - бессрочно';
                    else
                        $html = $html.'с '.$res->start_date.' по '.$res->end_date.'<br>';
                    return $html;
                }, 'format' => 'raw'],

                ['attribute' => 'license', 'label' => 'Способ распространения', 'value' => 'distributionType.name'],
                ['attribute' => 'license', 'label' => 'Вид лицензии', 'value' => 'license.name'],
                ['attribute' => 'registerName', 'label' => 'Регистратор', 'value' => function ($model) {
                    return $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'.'.mb_substr($model->register->patronymic, 0, 1).'.';
                },
                ],

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>

</div>


<script>
    function topsclr() {
        document.getElementById("content").scrollLeft = document.getElementById("topscrl").scrollLeft;
    }

    function bottomsclr() {
        document.getElementById("topscrl").scrollLeft = document.getElementById("content").scrollLeft;
    }
    window.onload = function() {
        document.getElementById("topfake").style.width = document.getElementById("content").scrollWidth + "px";
        document.getElementById("topscrl").style.display = "block";
        document.getElementById("topscrl").onscroll = topsclr;
        document.getElementById("content").onscroll = bottomsclr;
    };
</script>