<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="languages-index box">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
          <?= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
        </p>
      
        <div class="search-form" style="float: none;width: 100%;margin: 20px 0;">
          <form method="get" action="<?= \yii\helpers\Url::to(['languages/index']) ?>">
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
                <label for="rank">Code</label>
                <?= Html::input(
                  'text',
                  'code',
                  !empty(Yii::$app->request->get('code')) ? Yii::$app->request->get('code') : '',
                  ['class' => 'form-control']
                ) ?>
              </div>

            <div class="col-md-2">
                <label for="rank">Status</label>
                <select class="form-control" name="status" id="status">
                    <option value="" <?php echo empty(Yii::$app->request->get('status')) ? 'selected' : ''?>>All</option>
                    <?php foreach (\app\models\Languages::$statuses as $key => $label):?>
                        <option value="<?php echo $key?>"
                            <?php if(!empty(Yii::$app->request->get('status')) && Yii::$app->request->get('status') === $key) echo ' selected'?>
                        ><?php echo $label?></option>
                    <?php endforeach;?>
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
                  'title',
                  'code',
                    [
                        'attribute' => 'status',
                        'value' => function($data) {
                            return empty($data->status) ? '<span class="not-set">(not set)</span>' : \app\models\Languages::$statuses[$data->status];
                        },
                        'format' => 'html',
                    ],
                  [
                      'class' => 'yii\grid\ActionColumn',
                      'template' => '<div class="icon-action-wrapper">{view}</div><div class="icon-action-wrapper">{update}</div><div class="icon-action-wrapper">{delete}</div>',
                  ],
                ],
            ]); ?>
        </div>
    </div>
</div>
