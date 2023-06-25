<?php

/* @var $this yii\web\View */

use yii\helpers\Html;
?>
<div class="site-index box box-info">

  <div class="jumbotron">
    <h1>Welcome, <?= Yii::$app->user->identity['full_name']; ?>!</h1>
  </div>

</div>
