<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hashtag-index box">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
          <?= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

      <div class="search-form" style="float: none;width: 100%;margin: 20px 0;">
        <form method="get" action="<?= \yii\helpers\Url::to(['hash-tag/index']) ?>">
          <div class="row">

            <div class="col-md-2">
              <label for="rank">Name</label>
              <?= Html::input(
                'text',
                'name',
                !empty(Yii::$app->request->get('name')) ? Yii::$app->request->get('name') : '',
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
//                  'id',
                  'name',
                  [
                      'class' => 'yii\grid\ActionColumn',
                      'template' => '<div class="icon-action-wrapper">{view}</div><div class="icon-action-wrapper">{update}</div><div class="icon-action-wrapper">{delete}</div>',
                  ],
                ],
            ]); ?>
        </div>
    </div>
</div>
