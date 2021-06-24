<?php

use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchAsAdmin */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Реестр ПО';
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
            <?= Html::a('Добавить ПО', ['create'], ['class' => 'btn btn-success']) ?> <?= Html::a('Обновить статус лицензий', ['refresh-license'], ['class' => 'btn btn-warning']) ?>
    </p>


    <?php

    $gridColumns = [
        ['attribute' => 'id', 'label' => '№ п/п'],
        ['attribute' => 'asCompany', 'label' => 'Контрагент'],
        ['attribute' => 'contract_subject', 'label' => 'Предмет договора'],
        ['attribute' => 'price', 'label' => 'Сумма договора'],
        ['attribute' => 'document_date', 'label' => 'Дата договора'],
        ['attribute' => 'copyrightName', 'label' => 'Правообладатель', 'value' => 'copyright.name'],
        ['attribute' => 'as_name', 'label' => 'Наименование'],
        ['attribute' => 'license_count', 'label' => 'Кол-во лицензий'],
        ['attribute' => 'useYear', 'label' => 'Период использования', 'value' => function($model){
            $res = \app\models\work\UseYearsWork::find()->where(['as_admin_id' => $model->id])->one();
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
        ['attribute' => 'countryName', 'label' => 'Страна производитель', 'value' => 'countryProd.name'],
        ['attribute' => 'unifed_register_number', 'label' => 'Единый реестр ПО'],
        ['attribute' => 'distributionType', 'label' => 'Способ распространения'],
        ['attribute' => 'licenseTermType', 'label' => 'Срок лицензии'],
        ['attribute' => 'license', 'label' => 'Вид лицензии'],
        ['attribute' => 'inst_quant', 'label' => 'Установ.<br>Технопарк', 'value' => function($model){
            $res = \app\models\work\AsInstallWork::find()->where(['as_admin_id' => $model->id])->andWhere(['install_place_id' => 1])->all();
            $html = '';
            foreach ($res as $resOne)
                $html = $html.'Кабинет: '.$resOne->cabinet.' ('.$resOne->count.' шт.)<br>';
            return $html;
        }, 'format' => 'raw', 'encodeLabel' => false],
        ['attribute' => 'inst_tech', 'label' => 'Установ.<br>Кванториум', 'value' => function($model){
            $res = \app\models\work\AsInstallWork::find()->where(['as_admin_id' => $model->id])->andWhere(['install_place_id' => 2])->all();
            $html = '';
            foreach ($res as $resOne)
                $html = $html.'Кабинет: '.$resOne->cabinet.' ('.$resOne->count.' шт.)<br>';
            return $html;
        }, 'format' => 'raw', 'encodeLabel' => false],
        ['attribute' => 'inst_cdntt', 'label' => 'Установ.<br>ЦДНТТ', 'value' => function($model){
            $res = \app\models\work\AsInstallWork::find()->where(['as_admin_id' => $model->id])->andWhere(['install_place_id' => 3])->all();
            $html = '';
            foreach ($res as $resOne)
                $html = $html.'Кабинет: '.$resOne->cabinet.' ('.$resOne->count.' шт.)<br>';
            return $html;
        }, 'format' => 'raw', 'encodeLabel' => false],
        ['attribute' => 'inst_web', 'label' => 'WEB', 'value' => function($model){
            $res = \app\models\work\AsInstallWork::find()->where(['as_admin_id' => $model->id])->andWhere(['install_place_id' => 4])->all();
            $html = '';
            foreach ($res as $resOne)
                $html = $html.$resOne->count.' шт<br>';
            return $html;
        }, 'format' => 'raw', 'encodeLabel' => false],
        ['attribute' => 'reserved', 'label' => 'Резерв', 'value' => function ($model) {
            $res = \app\models\work\AsInstallWork::find()->where(['as_admin_id' => $model->id])->all();
            $sum = 0;
            foreach ($res as $resOne)
                $sum = $sum + $resOne->count;
            return $model->count - $sum;
        },
        ],
        ['attribute' => 'registerName', 'label' => 'Отв. лицо', 'value' => function ($model) {
            return $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'.'.mb_substr($model->register->patronymic, 0, 1).'.';
        },
        ],
        ['attribute' => 'comment', 'label' => 'Примечание']
    ];
    echo '<b>Скачать файл </b>';
    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,

        'options' => [
            'padding-bottom: 100px',
        ]
    ]);
    ?>

    <div id="topscrl">
        <div id="topfake"></div>
    </div>
    <div style="overflow-x: scroll; Width:100%" id="content">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [

                ['attribute' => 'id', 'label' => '№ п/п'],
                ['attribute' => 'as_company_id', 'label' => 'Контрагент', 'value' => function($model){return \app\models\work\AsCompanyWork::find()->where(['id' => $model->as_company_id])->one()->name;}],
                ['attribute' => 'document_number', 'label' => 'Договор №'],
                ['attribute' => 'document_date', 'label' => 'Дата договора', 'value' => function($model){return date("d.m.Y", strtotime($model->document_date));}],
                ['attribute' => 'price', 'label' => 'Сумма договора'],
                ['attribute' => 'contract_subject', 'label' => 'Предмет договора'],
                ['attribute' => 'copyright_id', 'label' => 'Правообладатель', 'value' => function($model){return \app\models\work\AsCompanyWork::find()->where(['id' => $model->copyright_id])->one()->name;}],
                ['attribute' => 'as_name', 'label' => 'Наименование'],
                ['attribute' => 'license_count', 'label' => 'Кол-во лицензий'],
                ['attribute' => 'useYear', 'label' => 'Период использования', 'value' => function($model){
                    $res = \app\models\work\UseYearsWork::find()->where(['as_admin_id' => $model->id])->one();
                    if ($res == null)
                        return '';
                    $html = '';
                    if ($res->start_date == '1999-01-01' && $res->end_date == '1999-01-01')
                        $html = 'Бессрочно';
                    else if ($res->end_date == '1999-01-01')
                        $html = $html.' '.date("d.m.Y", strtotime($res->start_date)).' - бессрочно';
                    else
                        $html = $html.'с '.date("d.m.Y", strtotime($res->start_date)).' по '.date("d.m.Y", strtotime($res->end_date)).'<br>';
                    return $html;
                }, 'format' => 'raw'],
                ['attribute' => 'country_name_id', 'label' => 'Страна производитель', 'value' => 'countryProd.name'],
                //['attribute' => 'unifed_register_number', 'label' => 'Единый реестр ПО'],
                //['attribute' => 'distribution_type_id', 'label' => 'Способ распространения', 'value' => function($model){return \app\models\work\DistributionTypeWork::find()->where(['id' => $model->distribution_type_id])->one()->name;}],
                ['attribute' => 'license_term_type_id', 'label' => 'Срок лицензии', 'value' => function($model){return \app\models\work\LicenseTermTypeWork::find()->where(['id' => $model->license_term_type_id])->one()->name;}],
                ['attribute' => 'license_id', 'label' => 'Вид лицензии', 'value' => function($model){return \app\models\work\LicenseWork::find()->where(['id' => $model->license_id])->one()->name;}],
                //['attribute' => 'license_status', 'label' => 'Статус лицензии', 'value' => function($model){return $model->license_status == 0 ? 'Неактивна' : 'Активна';}],
                ['attribute' => 'comment', 'label' => 'Примечание'],
                ['attribute' => 'register_id', 'label' => 'Отв. лицо', 'value' => function ($model) {
                    return $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'.'.mb_substr($model->register->patronymic, 0, 1).'.';
                },
                ],
                ['attribute' => 'inst_quant', 'label' => 'Установ.<br>Технопарк', 'value' => function($model){
                    $res = \app\models\work\AsInstallWork::find()->where(['as_admin_id' => $model->id])->andWhere(['install_place_id' => 1])->all();
                    $html = '';
                    foreach ($res as $resOne)
                        $html = $html.'Кабинет: '.$resOne->cabinet.' ('.$resOne->count.' шт.)<br>';
                    return $html;
                }, 'format' => 'raw', 'encodeLabel' => false],
                ['attribute' => 'inst_tech', 'label' => 'Установ.<br>Кванториум', 'value' => function($model){
                    $res = \app\models\work\AsInstallWork::find()->where(['as_admin_id' => $model->id])->andWhere(['install_place_id' => 2])->all();
                    $html = '';
                    foreach ($res as $resOne)
                        $html = $html.'Кабинет: '.$resOne->cabinet.' ('.$resOne->count.' шт.)<br>';
                    return $html;
                }, 'format' => 'raw', 'encodeLabel' => false],
                ['attribute' => 'inst_cdntt', 'label' => 'Установ.<br>ЦДНТТ', 'value' => function($model){
                    $res = \app\models\work\AsInstallWork::find()->where(['as_admin_id' => $model->id])->andWhere(['install_place_id' => 3])->all();
                    $html = '';
                    foreach ($res as $resOne)
                        $html = $html.'Кабинет: '.$resOne->cabinet.' ('.$resOne->count.' шт.)<br>';
                    return $html;
                }, 'format' => 'raw', 'encodeLabel' => false],
                ['attribute' => 'inst_web', 'label' => 'WEB', 'value' => function($model){
                    $res = \app\models\work\AsInstallWork::find()->where(['as_admin_id' => $model->id])->andWhere(['install_place_id' => 4])->all();
                    $html = '';
                    foreach ($res as $resOne)
                        $html = $html.$resOne->count.' шт<br>';
                    return $html;
                }, 'format' => 'raw', 'encodeLabel' => false],
                ['attribute' => 'reserved', 'label' => 'Резерв', 'value' => function ($model) {
                    $res = \app\models\work\AsInstallWork::find()->where(['as_admin_id' => $model->id])->all();
                    $sum = 0;
                    foreach ($res as $resOne)
                        $sum = $sum + $resOne->count;
                    return $model->license_count - $sum;
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