<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PartnerRule */

$this->title = 'Edit Partner Rule: ' . $model->title;
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="partner-rule-update box box-info">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <?= $this->render('_form', [
            'model' => $model
        ]) ?>
    </div>

</div>
