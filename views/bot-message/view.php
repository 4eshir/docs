<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\common\BotMessage */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Помощник', 'url' => ['index']];
\yii\web\YiiAsset::register($this);
?>

<style type="text/css">
    .main{
        border: 1px solid black;
        height: 700px;
        width: 60%;
    }

    .dialog{
        border: 1px solid black;
        margin: 15px;
        height: 83%;
    }

    .panel{
        border: 1px solid black;
        margin: 15px;
        height: 10%;
    }

    .message_bot{
        border: 1px solid black;
        margin: 10px;
        padding: 5px;
        width: 90%;
    }

    .message_user{
        border: 1px solid black;
        margin: 10px;
        padding: 5px;
        width: 90%;
        margin-left: auto;
        margin-right: 10px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.5.0.js"></script>

<div class="bot-message-view">

     <div class="main">
        <div class="dialog">
            <div class="message_bot">
                Выберите подходящий вариант:<br>
                1. Первый<br>
                2. Второй<br>
                3. Третий<br>
            </div>
            <div class="message_user">
                1. Первый
            </div>
        </div>

        <div class="panel">

        </div>

     </div>

     <button class="confirm_button" id="1">BUTTON</button>

</div>

<div id="comments">
   <p>1</p>
   <p>2</p>
</div>


<script type="text/javascript">
    $(".confirm_button").click(function(){
        var t = $(this).attr('id');
        console.log(t);
        $.ajax({
            type: "POST",
            url: '/index.php?r=bot-message/index&id='+t,
            success: function (data) {
              $('#comments').append(data);
            }
       });
        return false;
    });
</script>