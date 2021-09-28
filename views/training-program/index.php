<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchTrainingProgram */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Образовательные программы';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="training-program-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить программу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <?php
    $acc = \app\models\work\AccessLevelWork::find()->where(['user_id' => Yii::$app->user->identity->getId()])->andWhere(['access_id' => 21])->one();
    $visible = $acc !== null;

    echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($data) {
            if ($data['actual'] === 1)
                return ['class' => 'success'];
            else
                return ['class' => 'default'];
        },
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn', 'header' => 'Акт.', 'visible' => $visible,
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    $options['onclick'] = 'myStatus('.$model->id.');';
                    $options['checked'] = $model->actual ? true : false;
                    return $options;
                }],
            'name',
            ['attribute' => 'level', 'label' => 'Ур. сложности','value' => function ($model) {return $model->level+1;}],
            ['attribute' => 'branchs', 'label' => 'Место реализации', 'format' => 'html'],
            ['attribute' => 'ped_council_date', 'label' => 'Дата пед. сов.'],
            ['attribute' => 'ped_council_number', 'label' => '№ пед. сов.'],
            ['attribute' => 'compilers', 'format' => 'html'],
            'capacity',
            'studentAge',
            'stringFocus',
            ['attribute' => 'allowRemote', 'label' => 'Дист. тех.'],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>

    <?php
    $url = Url::toRoute(['training-program/actual']);
    $this->registerJs(
    "function myStatus(id){
        $.ajax({
            type: 'GET',
            url: 'index.php?r=training-program/actual',
            data: {id: id},
            success: function(result){
                console.log(result);
            }
        });
    }", yii\web\View::POS_END);
    ?>