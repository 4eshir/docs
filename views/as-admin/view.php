<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\work\AsAdminWork */

$this->title = $model->as_name;
$this->params['breadcrumbs'][] = ['label' => 'As Admins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="as-admin-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Редактировать', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Удалить', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['label' => '№ п/п', 'attribute' => 'id'],

            ['attribute' => 'as_company_id', 'label' => 'Контрагент', 'value' => function($model){return \app\models\work\AsCompanyWork::find()->where(['id' => $model->as_company_id])->one()->name;}],
            ['attribute' => 'contract_subject', 'label' => 'Предмет договора'],
            ['attribute' => 'price', 'label' => 'Сумма договора'],
            ['attribute' => 'document_date', 'label' => 'Дата договора', 'value' => function($model){return date("d.m.Y", strtotime($model->document_date));}],
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
            ['attribute' => 'country_name_id', 'label' => 'Страна производитель', 'value' => function($model){return $model->countryProd->name;}],            ['attribute' => 'unifed_register_number', 'label' => 'Единый реестр ПО'],
            ['attribute' => 'distribution_type_id', 'label' => 'Способ распространения', 'value' => function($model){return \app\models\work\DistributionTypeWork::find()->where(['id' => $model->distribution_type_id])->one()->name;}],
            ['attribute' => 'license_term_type_id', 'label' => 'Срок лицензии', 'value' => function($model){return \app\models\work\LicenseTermTypeWork::find()->where(['id' => $model->license_term_type_id])->one()->name;}],
            ['attribute' => 'license_id', 'label' => 'Вид лицензии', 'value' => function($model){return \app\models\work\LicenseWork::find()->where(['id' => $model->license_id])->one()->name;}],
            ['attribute' => 'license_status', 'label' => 'Статус лицензии', 'value' => function($model){return $model->license_status == 0 ? 'Неактивна' : 'Активна';}],
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
            ['attribute' => 'register_id', 'label' => 'Отв. лицо', 'value' => function ($model) {
                return $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'.'.mb_substr($model->register->patronymic, 0, 1).'.';
            },
            ],
            ['attribute' => 'comment', 'label' => 'Примечание'],
            ['label' => 'Договор (скан)', 'attribute' => 'scan', 'value' => function ($model) {
                return Html::a($model->scan, \yii\helpers\Url::to(['as-admin/get-file', 'fileName' => 'scan/'.$model->scan]));
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Лицензия', 'attribute' => 'license_file', 'value' => function ($model) {
                return Html::a($model->license_file, \yii\helpers\Url::to(['as-admin/get-file', 'fileName' => 'license/'.$model->license_file]));
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Коммерческие предложения', 'attribute' => 'commercialFiles', 'value' => function ($model) {
                $split = explode(" ", $model->commercial_offers);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['as-admin/get-file', 'fileName' => 'commercial_files/'.$split[$i]])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
            ['label' => 'Служебные записки', 'attribute' => 'serviceNoteFile', 'value' => function ($model) {
                $split = explode(" ", $model->service_note);
                $result = '';
                for ($i = 0; $i < count($split); $i++)
                    $result = $result.Html::a($split[$i], \yii\helpers\Url::to(['as-admin/get-file', 'fileName' => 'service_note/'.$split[$i]])).'<br>';
                return $result;
                //return Html::a($model->Scan, 'index.php?r=docs-out/get-file&filename='.$model->Scan);
            }, 'format' => 'raw'],
        ],
    ]) ?>

</div>
