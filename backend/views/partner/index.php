<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="partner-index box">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
          <?= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
      
        <div class="search-form" style="float: none;width: 100%;margin: 20px 0;">
          <form method="get" action="<?= \yii\helpers\Url::to(['partner/index']) ?>">
            <div class="row">
              <div class="col-md-2">
                <label for="rank">Legal Name</label>
                <?= Html::input(
                  'text',
                  'legalName',
                  !empty(Yii::$app->request->get('legalName')) ? Yii::$app->request->get('legalName') : '',
                  ['class' => 'form-control']
                ) ?>
              </div>
              
              <div class="col-md-2">
                <label for="rank">Billing VAT Number</label>
                <?= Html::input(
                  'text',
                  'billingVATNumber',
                  !empty(Yii::$app->request->get('billingVATNumber')) ? Yii::$app->request->get('billingVATNumber') : '',
                  ['class' => 'form-control']
                ) ?>
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
                  'legalName',
                  'billingVATNumber',
                  [
                      'class' => 'yii\grid\ActionColumn',
                      'template' => '<div class="icon-action-wrapper">{view}</div><div class="icon-action-wrapper">{update}</div><div class="icon-action-wrapper">{delete}</div>',
                  ],
                ],
            ]); ?>
        </div>
    </div>
</div>
