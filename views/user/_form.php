<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\work\UserWork */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'firstname')->textInput() ?>
    <?= $form->field($model, 'secondname')->textInput() ?>
    <?= $form->field($model, 'patronymic')->textInput() ?>
    <?= $form->field($model, 'username')->textInput() ?>
    <?php
    $people = \app\models\work\PeopleWork::find()->where(['company_id' => 8])->all();
    $items = \yii\helpers\ArrayHelper::map($people,'id','fullName');
    $params = [
        'prompt' => ''
    ];
    echo $form->field($model, "aka")->dropDownList($items,$params);

    ?>

    <h4><u>Административные права</u></h4>
    <div class="panel-default panel-body panel">
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 1])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'addUsers')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 2])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'viewRoles')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 3])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'editRoles')->checkbox(['checked' => $value]) ?>
    </div>

    <h4><u>Права доступа к системе документооборота</u></h4>
    <div class="panel-default panel-body panel">
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 4])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'viewOut')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 5])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'editOut')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 6])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'viewIn')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 7])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'editIn')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 8])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'viewOrder')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 9])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'editOrder')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 10])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'viewRegulation')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 11])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'editRegulation')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 12])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'viewEvent')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 13])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'editEvent')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 18])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'viewForeign')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 19])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'editForeign')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 20])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'viewProgram')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 21])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'editProgram')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 16])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'viewAdd')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 17])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'editAdd')->checkbox(['checked' => $value]) ?>
    </div>
    <h4><u>Права доступа к реестру ПО</u></h4>
    <div class="panel-default panel-body panel">
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 14])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'viewAS')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 15])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'editAS')->checkbox(['checked' => $value]) ?>
    </div>
    <h4><u>Права доступа к электронному журналу</u></h4>
    <div class="panel-default panel-body panel">
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 22])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'viewGroup')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 23])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'editGroup')->checkbox(['checked' => $value]) ?>

        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 24])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'viewGroupBranch')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 25])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'editGroupBranch')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 26])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'addGroup')->checkbox(['checked' => $value]) ?>
        <?php
        $tmp = \app\models\work\AccessLevelWork::find()->where(['user_id' => $model->id])->andWhere(['access_id' => 27])->one();
        $value = 0;
        if ($tmp != null) $value = true; else $value = false;
        ?>
        <?= $form->field($model, 'deleteGroup')->checkbox(['checked' => $value]) ?>

    </div>
    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
