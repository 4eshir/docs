<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SearchTrainingGroup */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Учебные группы';
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$access = [23, 25];
$isMethodist = \app\models\common\AccessLevel::find()->where(['user_id' => Yii::$app->user->identity->getId()])->andWhere(['in', 'access_id', $access])->one();
?>

<div class="training-group-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Добавить новую учебную группу', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?php $form = ActiveForm::begin(['action' => Url::to(['/training-group/index', 'archive' => '1'])]); ?>
    <?php if (\app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 10) || \app\models\components\RoleBaseAccess::CheckSingleAccess(Yii::$app->user->identity->getId(), 11)){
        echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
            'rowOptions' => function($data) {
                if ($data['archive'] === 1)
                    return ['style' => 'background: #c0c0c0'];
                else
                    return ['class' => $data['colorErrors']];
            },

        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn', 'header' => 'Архив',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    //$options['onclick'] = 'myStatus('.$model->id.');';
                    $options['checked'] = $model->archive ? true : false;
                    return $options;
                }],
            ['attribute' => 'numberView', 'format' => 'html'],
            ['attribute' => 'programName', 'format' => 'html'],
            ['attribute' => 'branchName', 'label' => 'Отдел', 'format' => 'raw'],
            ['attribute' => 'teachersList', 'format' => 'html'],
            'start_date',
            'finish_date',
            ['attribute' => 'budgetText', 'label' => 'Бюджет', 'filter' => [ 1 => "Бюджет", 0 => "Внебюджет"]],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    }
    else {
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'rowOptions' => function($data) {
                if ($data['archive'] === 1)
                    return ['style' => 'background: #c0c0c0'];
                else
                    return ['class' => $data['colorErrors']];
            },
            'columns' => [
                ['attribute' => 'numberView', 'format' => 'html'],
                ['attribute' => 'programName', 'format' => 'html'],
                ['attribute' => 'branchName', 'label' => 'Отдел', 'format' => 'raw'],
                ['attribute' => 'teachersList', 'format' => 'html'],
                'start_date',
                'finish_date',
                ['attribute' => 'budgetText', 'label' => 'Бюджет', 'filter' => [ 1 => "Бюджет", 0 => "Внебюджет"]],

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]);
    }
    ?>

    <div class="form-group">
        <?= Html::submitButton('Сохранить архив', ['class' => 'btn btn-success md-trigger', 'data-modal' => 'modal-12']) ?>
    </div>

    <?php ActiveForm::end(); ?>


</div>

<?php
$url = Url::toRoute(['training-group/archive']);
$this->registerJs(
    "function myStatus(id){
        $.ajax({
            type: 'GET',
            url: 'index.php?r=training-group/archive',
            data: {id: id},
            success: function(result){
                console.log(result);
            }
        });
    }", yii\web\View::POS_END);
?>