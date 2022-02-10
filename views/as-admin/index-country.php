<?php

use app\models\work\AsInstallWork;
use app\models\work\UseYearsWork;
use yii\helpers\Html;

/* @var $this yii\web\View */

$this->title = 'Страны производители';
$this->params['breadcrumbs'][] = ['label' => 'As Admins', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="as-admin-create">

    <h1><?= Html::encode($this->title).' '.Html::a('Добавить', ['add-country'], ['class' => 'btn btn-success']) ?></h1>
    <br>
    <table>
        <?php

        foreach ($model as $modelOne)
            echo '<tr><td style="padding-right: 10px">'.$modelOne->name.'</td><td>'.Html::a('Удалить', ['delete-country', 'model_id' => $modelOne->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Вы уверены?',
                        'method' => 'post',
                    ],]).'</td></tr>';

        ?>
    </table>


</div>
