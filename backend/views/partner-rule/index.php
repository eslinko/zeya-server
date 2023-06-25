<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\models\Partner;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-rule-index box">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
          <?= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
      
        <div class="search-form" style="float: none;width: 100%;margin: 20px 0;">
          <form method="get" action="<?= \yii\helpers\Url::to(['partner-rule/index']) ?>">
            <div class="row">
              <div class="col-md-2">
                <label for="rank">Title</label>
                <?= Html::input(
                  'text',
                  'title',
                  !empty(Yii::$app->request->get('title')) ? Yii::$app->request->get('title') : '',
                  ['class' => 'form-control']
                ) ?>
              </div>
              
              <div class="col-md-2">
                <label for="rank">Trigger Name</label>
                <?= Html::input(
                  'text',
                  'triggerName',
                  !empty(Yii::$app->request->get('triggerName')) ? Yii::$app->request->get('triggerName') : '',
                  ['class' => 'form-control']
                ) ?>
              </div>
              
              <div class="col-md-2">
                <label for="rank">Emission Calculation Base Value</label>
                <?= Html::input(
                  'text',
                  'emissionCalculationBaseValue',
                  !empty(Yii::$app->request->get('emissionCalculationBaseValue')) ? Yii::$app->request->get('emissionCalculationBaseValue') : '',
                  ['class' => 'form-control']
                ) ?>
              </div>
              
              <div class="col-md-2">
                <label for="rank">Emission Calculation Percentage</label>
                <?= Html::input(
                  'text',
                  'emissionCalculationPercentage',
                  !empty(Yii::$app->request->get('emissionCalculationPercentage')) ? Yii::$app->request->get('emissionCalculationPercentage') : '',
                  ['class' => 'form-control']
                ) ?>
              </div>
              
              <div class="col-md-2">
                <label for="rank">Partner</label>
                <?php
                  echo Select2::widget([
                    'value' => Yii::$app->request->get('partnerId'),
                    'name' => 'partnerId',
                    'data' => ArrayHelper::map(Partner::find()->all(),'id','legalName'),
                    'options' => ['placeholder' => 'Select Partner'],
                    'pluginOptions' => [
                      'allowClear' => true
                    ],
                  ]);
                ?>
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
                  'id',
                  [
                    'attribute' => 'partnerId',
                    'value' => function($data) {
                      $partner = Partner::findOne($data->partnerId);
                      return !empty($partner) ? Html::a($partner->legalName, ['partner/view', 'id' => $partner->id]) : '<span class="not-set">(not set)</span>';
                    },
                    'format' => 'html'
                  ],
                  'title',
                  'triggerName',
                  'emissionCalculationBaseValue',
                  'emissionCalculationPercentage',
                  [
                      'class' => 'yii\grid\ActionColumn',
                      'template' => '<div class="icon-action-wrapper">{view}</div><div class="icon-action-wrapper">{update}</div><div class="icon-action-wrapper">{delete}</div>',
                  ],
                ],
            ]); ?>
        </div>
    </div>
</div>
