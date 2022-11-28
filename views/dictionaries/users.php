<?php

use yii\helpers\Html;


?>

<?php
$this->title = 'Список пользователей / Роли';
$this->params['breadcrumbs'][] = $this->title;
?>

<h3><u>Список пользователей / Роли</u></h3>

<ol class="ball" style="list-style-type: none;">
    <li><?php echo Html::a('Список пользователей', \yii\helpers\Url::to(['user/index'])) ?></li>
    <li><?php echo Html::a('Роли', \yii\helpers\Url::to(['role/index'])) ?></li>                
</ol>

<style>
.ball {
list-style: none;
margin: 0;
}
.ball a {
width: 100%;
color: black;
text-decoration: none;
display: inline-block;
padding-left: 25px;
height: 44px;
line-height: 44px;
font-size: 20px;
position: relative;
transition: .3s linear;

display: inline-block;
line-height: 2;
text-decoration: none;
cursor: pointer;
}

.ball a:after {
background-color: #EC351D;
display: block;
content: "";
height: 3px;
width: 0%;
-webkit-transition: width .3s ease-in-out;
-moz--transition: width .3s ease-in-out;
transition: width .3s ease-in-out;
}

.ball a:before {
content: "";
width: 30px;
height: 30px;
border-radius: 50%;
background: #425273;
position: absolute;
left: -30px;
top: 7px;
}

.ball a:hover:after, .ball a:focus:after {
    width: 20%;
}

.ball li {position: relative;}
.ball li:before {
content: "";
width: 20px;
height: 20px;
border-radius: 50%;
background: #EC351D;
position: absolute;
top: 12px;
left: -30px;
z-index: 2;
transition: .4s ease-in-out;
}
.ball li:hover:before {left: -20px;}
</style>