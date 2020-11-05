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
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
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
            ['label' => 'Реквизиты', 'attribute' => 'as_company_id', 'value' => function($model){
                return 'Компания: '.$model->asCompany->name.'<br>Номер документа: '.$model->document_number.'<br>Дата документа: '.$model->document_date;
            }, 'format' => 'raw'],
            ['label' => 'Кол-во экземпляров', 'attribute' => 'count'],
            ['label' => 'Цена за 1 шт.', 'attribute' => 'price'],
            ['label' => 'Стоимость', 'attribute' => 'cost', 'value' => function($model){
                return $model->count * $model->price;
            }],
            ['label' => 'Установлено в "Кванториум"', 'attribute' => 'inst_quant', 'value' => function($model){
                $res = \app\models\common\AsInstall::find()->where(['as_admin_id' => $model->id])->andWhere(['branch_id' => 1])->all();
                $html = '';
                foreach ($res as $resOne)
                    $html = $html.'Кабинет: '.$resOne->cabinet.' '.$resOne->count.' шт.<br>';
                return $html;
            }, 'format' => 'raw'],
            ['label' => 'Установлено в "Технопарк"', 'attribute' => 'inst_tech', 'value' => function($model){
                $res = \app\models\common\AsInstall::find()->where(['as_admin_id' => $model->id])->andWhere(['branch_id' => 2])->all();
                $html = '';
                foreach ($res as $resOne)
                    $html = $html.'Кабинет: '.$resOne->cabinet.' '.$resOne->count.' шт.<br>';
                return $html;
            }, 'format' => 'raw'],
            ['label' => 'Установлено в "ЦДНТТ"', 'attribute' => 'inst_cdntt', 'value' => function($model){
                $res = \app\models\common\AsInstall::find()->where(['as_admin_id' => $model->id])->andWhere(['branch_id' => 3])->all();
                $html = '';
                foreach ($res as $resOne)
                    $html = $html.'Кабинет: '.$resOne->cabinet.' '.$resOne->count.' шт.<br>';
                return $html;
            }, 'format' => 'raw'],
            ['attribute' => 'countryProd', 'label' => 'Страна производитель', 'value' => $model->countryProd->name],
            ['label' => 'Годы использования','attribute' => 'useYear',  'value' => function($model){
                $res = \app\models\common\UseYears::find()->where(['as_admin_id' => $model->id])->all();
                $html = '';
                foreach ($res as $resOne)
                    $html = $html.'с '.$resOne->start_date.' по '.$resOne->end_date.'<br>';
                if ($html == 'с 1999-01-01 по 1999-01-01<br>')
                    return 'Бессрочно';
                return $html;
            }, 'format' => 'raw'],
            ['label' => 'Срок лицензии', 'attribute' => 'license_date', 'value' => function($model){
                return 'с '.$model->license_start.' по '.$model->license_finish;
            }],
            ['label' => 'Тип лицензии', 'attribute' => 'license', 'value' => $model->license->name],
            ['label' => 'Договор (скан)', 'attribute' => 'scan', 'value' => function ($model) {
                return Html::a($model->scan, \yii\helpers\Url::to(['as-admin/get-file', 'fileName' => 'scan/'.$model->scan]));
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
