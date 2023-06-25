<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\models\PartnerRule;
use common\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-rule-action-index box">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
      <p>
		    <?= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
      </p>
      
        <div class="search-form" style="float: none;width: 100%;margin: 20px 0;">
          <form method="get" action="<?= \yii\helpers\Url::to(['partner-rule-action/index']) ?>">
            <div class="row">
              <div class="col-md-2">
                <label for="rank">Rule</label>
                <?php
                  echo Select2::widget([
                    'value' => Yii::$app->request->get('ruleId'),
                    'name' => 'ruleId',
                    'data' => ArrayHelper::map(PartnerRule::find()->all(),'id','title'),
                    'options' => ['placeholder' => 'Select Rule'],
                    'pluginOptions' => [
                      'allowClear' => true
                    ],
                  ]);
                ?>
              </div>

              <div class="col-md-2">
                <label for="rank">User</label>
                <?php
                  echo Select2::widget([
                    'value' => Yii::$app->request->get('emittedLovestarsUser'),
                    'name' => 'emittedLovestarsUser',
                    'data' => ArrayHelper::map(User::find()->all(),'id','full_name'),
                    'options' => ['placeholder' => 'Select User'],
                    'pluginOptions' => [
                      'allowClear' => true
                    ],
                  ]);
                ?>
              </div>

              <div class="col-md-2">
                <?php
                  $approvalStatus = Yii::$app->request->get('approvalStatus');
                  if($approvalStatus === NULL || (empty($approvalStatus) && $approvalStatus !== '0')) {
                    $approvalStatus = NULL;
                  } else if ($approvalStatus === '0') $approvalStatus = 'no';
                ?>
                <label for="rank">Approval Status</label>
                <select name="approvalStatus" id="approvalStatus" class="form-control">
                  <option value=""
                    <?php echo Yii::$app->request->get('approvalStatus') === NULL ? 'selected' : '';?>
                  >All</option>
                  <option value="0"
                    <?php echo $approvalStatus == 'no' ? 'selected' : '';?>
                  >No</option>
                  <option value="1"
                    <?php echo $approvalStatus == 'yes' ? 'selected' : '';?>
                  >Yes</option>
                </select>
              </div>
  
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
                  ['class' => 'yii\grid\SerialColumn'],
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
                  [
                    'class' => 'yii\grid\ActionColumn',
                    'template' => '<div class="icon-action-wrapper">{view}</div>',
                  ],
                ],
            ]); ?>
        </div>
    </div>
</div>
