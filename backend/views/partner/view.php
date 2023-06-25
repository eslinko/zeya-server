<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Partner */

$this->title = 'Partner: ' . $model->legalName;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-view box box-warning">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete the partner?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Back', ['index'] ,['class' => 'btn btn-warning']) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
              'id',
              'legalName',
              'billingVATNumber',
              [
                'attribute' => 'description',
                'value' => function($data) {
                  return !$data->description ? '<span class="not-set">(not set)</span>' : $data->description;
                },
                'format' => 'html',
              ],
              [
                'attribute' => 'billingDetails',
                'value' => function($data) {
                  return !$data->billingDetails ? '<span class="not-set">(not set)</span>' : $data->billingDetails;
                },
                'format' => 'html',
              ],
            ],
        ]) ?>
    </div>
</div>
