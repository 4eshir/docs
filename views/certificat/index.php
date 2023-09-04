 <?php

use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchCertificat */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сертификаты';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="certificat-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
            echo Html::a('Добавить сертифкат(-ы)', ['create'], ['class' => 'btn btn-success'])
        ?>
    </p>

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <div style="margin-bottom: 10px;">
        <?php

        $gridColumns = [
            ['attribute' => 'certificatView', 'format' => 'raw'],
            ['attribute' => 'certificatTemplateName', 'format' => 'raw'],
            ['attribute' => 'participantName', 'label' => 'Отдел', 'format' => 'raw'],
            ['attribute' => 'participantGroup', 'format' => 'raw'],
            ['attribute' => 'participantProtection', 'format' => 'raw'],

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
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn', 'header' => '№ п/п'],
            //'id',
            ['attribute' => 'certificatView', 'format' => 'raw'],
            ['attribute' => 'certificatTemplateName', 'format' => 'raw'],
            //'participantName',
            ['attribute' => 'participantName', 'format' => 'raw'],
            ['attribute' => 'participantGroup', 'format' => 'raw'],
            ['attribute' => 'participantProtection', 'format' => 'raw'],

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>


<?php
/*if(isset($_GET['ok'])) {
    $cert = new \app\models\work\CertificatWork();
    $cert->archiveDownload();
}*/
?>
