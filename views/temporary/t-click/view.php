<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\temporary\TClick;

/* @var $this yii\web\View */
/* @var $model app\models\temporary\TClick */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => 'T Clicks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div id="tclick-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <div style="height: 200px;">
        <?php 
        if ($model->name !== 'Админ')
            echo Html::a('ЖМЯК', ['update', 'id' => $model->id], ['class' => 'btn btn-danger', 'style' => 'height: 70%; width: 80%; line-height: 120px']);
        else
        {
            echo '<a class="btn btn-success" href="#" style="height: 50px; width: 50px; font-size: 25px; margin-bottom: 30px;" onclick="refresh()">♺</a>';

            $users = TClick::find()->where(['!=', 'name', 'Админ'])->orderBy(['time' => SORT_ASC])->all();
            echo '<table class="table table-condensed">';
            foreach ($users as $user)
                echo '<tr><td>'.$user->name.'</td><td>'.$user->time.'</td></tr>';
            echo '</table>';

            echo Html::a('СБРОСИТЬ ВСЕ!', ['break'], ['class' => 'btn btn-danger', 'style' => 'height: 70%; width: 80%; line-height: 120px']);
        }

        ?>
        
    </div>


</div>


<script type="text/javascript">
    function refresh()
    {
        location.reload();
    }
    
</script>