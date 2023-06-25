<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Events */

$this->title = 'Edit Event: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="events-update box box-info">
    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model
        ]) ?>
    </div>

</div>
