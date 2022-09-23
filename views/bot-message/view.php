<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\work\BotMessageWork;
use app\models\work\BotMessageVariantWork;

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

    .message_option_text
    {
        font-weight: bolder;
    }

    .message_answer_text
    {
        
    }

    .message_option_button
    {
        margin-top: 10px;
    }
</style>

<script src="https://code.jquery.com/jquery-3.5.0.js"></script>

<?php 
    $firstMessage = BotMessageWork::find()->where(['id' => 1])->one();
    $firstVariants = BotMessageVariantWork::find()->where(['bot_message_id' => $firstMessage->id])->all();
?>

<div class="bot-message-view">

     <div class="main">
        <div class="dialog" id="dialog_window">
            <div class="message_bot">
                <span class="message_option_text"><?php echo $firstMessage->text; ?></span><br/>
                <?php
                    foreach ($firstVariants as $variant)
                        echo '<button class="confirm_button message_option_button" id="'.$variant->id.'">'.$variant->text.'</button>';
                ?>
            </div>
        </div>

        <div class="panel">

        </div>

     </div>

</div>

<script type="text/javascript">
    $(".confirm_button").click(function(){
        var t = $(this).attr('id');
        console.log(t);
        $.ajax({
            type: "POST",
            url: '/index.php?r=bot-message/next-message&id='+t,
            success: function (data) {
              $('#dialog_window').append(data);
            }
       });
        return false;
    });
</script>