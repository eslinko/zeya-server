<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\PartnerRuleAction;
use common\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="lovestar-index box">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <div class="search-form" style="float: none;width: 100%;margin: 20px 0;">
          <form method="get" action="<?= \yii\helpers\Url::to(['lovestar/index']) ?>">
            <div class="row">
              <div class="col-md-2">
                <label for="rank">Current Owner</label>
                <?php
                  echo Select2::widget([
                    'value' => Yii::$app->request->get('currentOwner'),
                    'name' => 'currentOwner',
                    'data' => ArrayHelper::map(User::find()->all(),'id','full_name'),
                    'options' => ['placeholder' => 'Select Owner'],
                    'pluginOptions' => [
                      'allowClear' => true
                    ],
                  ]);
                ?>
              </div>

<!--              <div class="col-md-2">-->
<!--                <label for="rank">Issing Action</label>-->
<!--                --><?php
//                  echo Select2::widget([
//                    'value' => Yii::$app->request->get('issuingAction'),
//                    'name' => 'issuingAction',
//                    'data' => ArrayHelper::map(PartnerRuleAction::find()->all(),'id','ruleTitle'),
//                    'options' => ['placeholder' => 'Select Action'],
//                    'pluginOptions' => [
//                      'allowClear' => true
//                    ],
//                  ]);
//                ?>
<!--              </div>-->
  
              <div class="col-md-2">
                <button type="submit">Search</button>
              </div>
            </div>
          </form>
        </div>

        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                  'id',
                  [
                    'attribute' => 'issuingAction',
                    'value' => function($data) {
                      $action = PartnerRuleAction::findOne($data->issuingAction);
                      return !empty($action) ? Html::a($action->ruleTitle, ['user/view', 'id' => $action->id]) : '<span class="not-set">(not set)</span>';
                    },
                    'format' => 'html'
                  ],
                  [
                    'attribute' => 'currentOwner',
                    'value' => function($data) {
                      $user = User::findOne($data->currentOwner);
                      return !empty($user) ? Html::a($user->full_name, ['user/view', 'id' => $user->id]) : '<span class="not-set">(not set)</span>';
                    },
                    'format' => 'html'
                  ],
                  [
                    'attribute' => 'birthTimestamp',
                    'value' => function($data) {
                      return date('Y.m.d H:i:s', $data->birthTimestamp);
                    },
                    'format' => 'html'
                  ],
//                  [
//                      'class' => 'yii\grid\ActionColumn',
//                      'template' => '<div class="icon-action-wrapper">{view}</div><div class="icon-action-wrapper">{update}</div><div class="icon-action-wrapper">{delete}</div>',
//                  ],
                ],
            ]); ?>
        </div>
    </div>
</div>
