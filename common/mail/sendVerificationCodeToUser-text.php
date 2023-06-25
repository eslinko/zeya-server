<?php

/* @var $this yii\web\View */
/* @var $user common\models\User */

?>
Hello <?= $user->username ?>,

At the bottom there is your verification code:

<?= $user->verificationCode ?>
