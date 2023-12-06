<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\InvitationCodes */

$this->title = 'New Teacher Outcome';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invitation-codes-create box box-success">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <?= $this->render('_form', [
          'model' => $model
        ]) ?>
    </div>

</div>
