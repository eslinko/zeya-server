<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\PartnerRule;
use common\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\PartnerRuleAction */

$this->title = 'Action: #' . $model->id;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-rule-view box box-warning">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('Back', ['index'], ['class' => 'btn btn-warning']) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
              'id',
              [
                'attribute' => 'ruleId',
                'value' => function($data) {
                  $partnerRule = PartnerRule::findOne($data->ruleId);
                  return !empty($partnerRule) ? Html::a($partnerRule->title, ['partner-rule/view', 'id' => $partnerRule->id]) : '<span class="not-set">(not set)</span>';
                },
                'format' => 'html'
              ],
              'emittedLovestars',
              [
                'attribute' => 'emittedLovestarsUser',
                'value' => function($data) {
                  $user = User::findOne($data->emittedLovestarsUser);
                  return !empty($user) ? Html::a($user->full_name, ['user/view', 'id' => $user->id]) : '<span class="not-set">(not set)</span>';
                },
                'format' => 'html'
              ],
              'ruleTitle',
              'triggerName',
              'emissionCalculationBaseValue',
              'emissionCalculationPercentage',
              [
                'attribute' => 'timestamp',
                'value' => function($data) {
                  return date('Y.m.d H:i:s', $data->timestamp);
                },
                'format' => 'html'
              ],
              [
                'attribute' => 'approvalStatus',
                'value' => function($data) {
                  return $data->approvalStatus ? '<span class="text-success">Yes</span>' : '<span class="not-set">No</span>';
                },
                'format' => 'html'
              ],
            ],
        ]) ?>
    </div>
</div>
