<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchAsAdmin */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'ПО "Административный процесс';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="as-admin-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить ПО', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div style="overflow: auto; overflow-y: scroll; Height: 500px; Width:100%">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [

                ['attribute' => 'id', 'label' => '№ п/п'],
                ['attribute' => 'as_name', 'label' => 'Наименование'],
                ['attribute' => 'requisites', 'label' => 'Реквизиты', 'value' => function($model){
                    return 'Компания: '.$model->asCompany->name.'<br>Номер док.: '.$model->document_number.'<br>Дата док.: '.$model->document_date;
                }, 'format' => 'raw'],
                ['attribute' => 'count', 'label' => 'Кол-во'],
                ['attribute' => 'price', 'label' => 'Цена'],
                ['attribute' => 'cost', 'label' => 'Стоимость', 'value' => function($model){
                    return $model->count * $model->price;
                }],
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
                    $html = '';
                    if ($res->start_date == '1999-01-01' && $res->end_date == '1999-01-01')
                        $html = 'Бессрочно';
                    else
                        $html = $html.'с '.explode("-", $res->start_date).' по '.explode("-", $res->end_date).'<br>';
                    return $html;
                }, 'format' => 'raw'],
                ['attribute' => 'license_date', 'label' => 'Срок лицензии', 'value' => function($model){
                    return 'с '.explode("-", $model->license_start)[0].' по '.explode("-", $model->license_finish)[0];
                }],
                ['attribute' => 'license', 'label' => 'Тип лицензии', 'value' => 'license.name'],
                ['attribute' => 'scan', 'label' => 'Договор (скан)'],
                ['attribute' => 'registerName', 'label' => 'Регистратор', 'value' => function ($model) {
                    return $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'.'.mb_substr($model->register->patronymic, 0, 1).'.';
                },
                ],

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>

</div>
