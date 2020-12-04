<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\AsAdmin */

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
            ['label' => 'Правообладатель', 'attribute' => 'copyright.name'],
            ['label' => 'Реквизиты', 'attribute' => 'as_company_id', 'value' => function($model){
                return 'Компания: '.$model->asCompany->name.'<br>Номер документа: '.$model->document_number.'<br>Дата документа: '.$model->document_date;
            }, 'format' => 'raw'],
            ['label' => 'Кол-во экземпляров', 'attribute' => 'count'],
            ['label' => 'Стоимость', 'attribute' => 'price'],
            ['label' => 'Годы использования','attribute' => 'useYear',  'value' => function($model){
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
            ['attribute' => 'countryProd', 'label' => 'Страна производитель', 'value' => $model->countryProd->name],
            ['attribute' => 'unifed_register_number', 'label' => 'Единый реестр ПО'],
            ['attribute' => 'license', 'label' => 'Способ распространения', 'value' => $model->distributionType->name],
            ['attribute' => 'time', 'label' => 'Срок лицензии', 'value' => function($model){
                $res = \app\models\common\UseYears::find()->where(['as_admin_id' => $model->id])->one();
                if ($res->start_date !== '1999-01-01' && $res->end_date !== '1999-01-01')
                    return 'Срочная';
                else
                    return 'Бессрочная';
            }],
            ['label' => 'Вид лицензии', 'attribute' => 'license', 'value' => $model->license->name],
            ['label' => 'Установлено в "Кванториум"', 'attribute' => 'inst_quant', 'value' => function($model){
                $res = \app\models\common\AsInstall::find()->where(['as_admin_id' => $model->id])->andWhere(['branch_id' => 1])->all();
                $html = '';
                foreach ($res as $resOne)
                    $html = $html.'Кабинет: '.$resOne->cabinet.' ('.$resOne->count.' шт.)<br>';
                return $html;
            }, 'format' => 'raw'],
            ['label' => 'Установлено в "Технопарк"', 'attribute' => 'inst_tech', 'value' => function($model){
                $res = \app\models\common\AsInstall::find()->where(['as_admin_id' => $model->id])->andWhere(['branch_id' => 2])->all();
                $html = '';
                foreach ($res as $resOne)
                    $html = $html.'Кабинет: '.$resOne->cabinet.' ('.$resOne->count.' шт.)<br>';
                return $html;
            }, 'format' => 'raw'],
            ['label' => 'Установлено в "ЦДНТТ"', 'attribute' => 'inst_cdntt', 'value' => function($model){
                $res = \app\models\common\AsInstall::find()->where(['as_admin_id' => $model->id])->andWhere(['branch_id' => 3])->all();
                $html = '';
                foreach ($res as $resOne)
                    $html = $html.'Кабинет: '.$resOne->cabinet.' ('.$resOne->count.' шт.)<br>';
                return $html;
            }, 'format' => 'raw'],
            ['label' => 'Примечание', 'attribute' => 'comment', 'value' => $model->comment],
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
            ['label' => 'Регистратор', 'attribute' => 'registerName', 'value' => function ($model) {
                return $model->register->secondname.' '.mb_substr($model->register->firstname, 0, 1).'.'.mb_substr($model->register->patronymic, 0, 1).'.';
            },
            ],
        ],
    ]) ?>

</div>
