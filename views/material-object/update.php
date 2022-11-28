<?php

use app\models\work\MaterialObjectSubobjectWork;
use app\models\work\SubobjectWork;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\common\MaterialObject */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Редактировать объект: ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => 'Материальные ценности', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Редактирование';
?>
<div class="material-object-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php /*$this->render('_form', [
        'model' => $model,
    ])*/ ?>

    <?php $form = ActiveForm::begin(); ?>

    <div id="inventory_number" style="display: <?php echo $model->inventory_number != '' ? 'block' : 'none';?>">
        <?php echo '<h2> Инвентарный номер: '.$model->inventory_number.'</h2>'; ?>
    </div>

    <?= $form->field($model, 'photoFile')->fileInput(['multiple' => false]) ?>

    <?= $form->field($model, 'is_education')->checkbox() ?>

    <?= $form->field($model, 'damage')->textarea(['rows' => '5']) ?>

    <div id="state-div" style="display: <?php echo $model->type == 2 ? 'block' : 'none'; ?>">
        <?= $form->field($model, 'state')->textInput(['type' => 'number', 'style' => 'width: 30%']) ?>
    </div>

    <?= $form->field($model, 'status')->checkbox(); ?>

    <?php
    $items = [0 => 'Списание не требуется', 1 => 'Готов к списанию', 2 => 'Списан'];
    $params = [
        'style' => 'width: 30%'
    ];
    echo $form->field($model, 'write_off')->dropDownList($items,$params);

    ?>

    <div <?php echo $model->complex == 1 ? '' : 'hidden'; ?>>
        <?php
        $parentObj = MaterialObjectSubobjectWork::find()->where(['material_object_id' => $model->id])->all();

        if ($parentObj !== null)
        {
            echo '<table class="table table-bordered">';
            echo '<tr style="width: 30px; font-weight: 600;"><td style="width: 6%;">№ п/п</td><td>Название компонентов</td><td>Описание</td><td>Состояние</td></tr>';
            $i = 1;
            foreach ($parentObj as $one)
            {
                echo '<tr><td>'.$i.'</td><td>'.$one->subobjectWork->name.'</td><td>'
                    .$form->field($model, 'subObjectArr[]')->textInput(['value' => $one->subobjectWork->characteristics])->label(false)
                    .'</td><td>'.'<select name="state"><option value="0">Не рабочий</option><option value="1">Рабочий</option></select>'

                    /*$one->subobjectWork->stateString*/.'</td></tr>';
                $subs = SubobjectWork::find()->where(['parent_id' => $one->subobjectWork->id])->all();
                if ($subs !== null)
                {
                    $j = 1;
                    foreach ($subs as $sub)
                    {
                        echo '<tr><td>'.$i.'.'.$j.'</td><td>'.$sub->name.'</td><td>'./*$sub->characteristics*/
                            $form->field($model, 'subObjectArr[]')->textInput(['value' => $sub->characteristics])->label(false)
                            .'</td><td>'.$sub->stateString.'</td></tr>';
                        $j++;
                    }
                }
                $i++;
            }
            echo '</table>';
        }
        ?>
    </div>

    <div class="form-group">
        <?= Html::submitButton('Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
