<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */
/* @var $exception Exception */

$this->title = $name;
?>
<section class="content">

    <div class="error-page">
        <h2 class="headline text-info"><i class="fa fa-warning text-yellow"></i></h2>

        <div class="error-content">
            <h3><?= $name ?></h3>

            <p>
                <?php //nl2br(Html::encode($message)) ?>
            </p>

            <p>
              The server cannot retrieve data about the requested page. Perhaps this page does not exist. <br>
              Back to the <a href='<?= Yii::$app->homeUrl ?>'>main page</a>.
            </p>

        </div>
    </div>

</section>
