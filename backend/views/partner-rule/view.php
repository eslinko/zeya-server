<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\PartnerRule */

$this->title = 'Partner: ' . $model->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-rule-view box box-warning">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete the rule?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Back', ['index'], ['class' => 'btn btn-warning']) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
              'id',
              [
                'attribute' => 'partnerId',
                'value' => function($data) {
                  $partner = \app\models\Partner::findOne($data->partnerId);
                  return !empty($partner) ? Html::a($partner->legalName, ['partner/view', 'id' => $partner->id]) : '<span class="not-set">(not set)</span>';
                },
                'format' => 'html'
              ],
              'title',
              'triggerName',
              'emissionCalculationBaseValue',
              'emissionCalculationPercentage',
            ],
        ]) ?>
    </div>
</div>
