<?php
use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $user common\models\User */
?>
<div class="password-reset">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <p>At the bottom there is your verification code:</p>

    <p><?= Html::encode($user->verificationCode) ?></p>
</div>
