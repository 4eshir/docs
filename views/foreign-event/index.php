<?php

use kartik\export\ExportMenu;
use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchForeignEvent */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Учет достижений в мероприятиях';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="foreign-event-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php //echo Html::a('Добавить', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <!--<div style="margin: 0 118%;">
        <div class="" data-html="true" style="position: fixed; z-index: 101; width: 30px; height: 30px; padding: 5px 0 0 0; background: #09ab3f; color: white; text-align: center; display: inline-block; border-radius: 4px;" title="Желтый цвет - карточка учета достижений имеет ошибку">❔</div>
    </div>-->

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php

    $gridColumns = [
        'name',
        ['attribute' => 'companyString'],
        'start_date',
        'finish_date',
        'city',
        'eventWayString',
        'eventLevelString',

        ['attribute' => 'teachersExport', 'contentOptions' => ['class' => 'text-nowrap']],

        ['attribute' => 'participantCount', 'format' => 'raw', 'label' => 'Кол-во участников', 'encodeLabel' => false],
        ['attribute' => 'winners', 'contentOptions' => ['class' => 'text-nowrap']],
        ['attribute' => 'prizes', 'contentOptions' => ['class' => 'text-nowrap']],
        'businessTrips',

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
    <div style="margin-bottom: 10px">

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($data) {
            if ($data['errorsWork'] !== '')
                return ['class' => 'warning'];
        },
        'columns' => [

            'name',
            ['attribute' => 'companyString'],
            'start_date',
            'finish_date',
            'city',
            'eventWayString',
            'eventLevelString',

            ['attribute' => 'teachers', 'format' => 'raw', 'contentOptions' => ['class' => 'text-nowrap']],

            ['attribute' => 'participantCount', 'format' => 'raw', 'label' => 'Кол-во<br>участников', 'encodeLabel' => false],
            ['attribute' => 'winners', 'format' => 'raw', 'contentOptions' => ['class' => 'text-nowrap']],
            ['attribute' => 'prizes', 'format' => 'raw', 'contentOptions' => ['class' => 'text-nowrap']],
            'businessTrips',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
