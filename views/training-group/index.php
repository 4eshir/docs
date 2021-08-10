<?php

use yii\helpers\Html;
use yii\grid\GridView;
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

    <?php if ($isMethodist !== null){
        $form = ActiveForm::begin(['action'=>['archive'], 'method'=>"post"]);
        echo GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
            'rowOptions' => function($data) {
                if ($data['archive'] === 1)
                    return ['class' => 'danger'];
                else
                    return ['class' => 'default'];
            },
        'columns' => [
            ['class' => 'yii\grid\CheckboxColumn', 'header' => 'Архив', 'checkboxOptions' => function($model) {
                return $model->archive === 1 ? ['checked' => 'true'] : [];
            },],
            'number',
            ['attribute' => 'programNameNoLink', 'format' => 'html'],
            ['attribute' => 'branchName', 'label' => 'Отдел', 'format' => 'raw'],
            ['attribute' => 'teachersList', 'format' => 'html'],
            'start_date',
            'finish_date',
            ['attribute' => 'budgetText', 'label' => 'Бюджет', 'filter' => [ 1 => "Бюджет", 0 => "Внебюджет"]],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
        echo Html::submitButton('Архивировать', ['class' => 'btn btn-danger']);
        ActiveForm::end();
    }
    else {
        echo GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                'number',
                ['attribute' => 'programNameNoLink', 'format' => 'html'],
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


</div>
