<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Teacher;
use common\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teaching-transaction-index box">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
<!--      <p>-->
<!--		    --><?php //= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
<!--      </p>-->
      
        <div class="search-form" style="float: none;width: 100%;margin: 20px 0;">
          <form method="get" action="<?= \yii\helpers\Url::to(['teaching-transaction/index']) ?>">
            <div class="row">
<!--              <div class="col-md-2">-->
<!--                <label for="rank">Previous Owner</label>-->
<!--                --><?php
//                  echo Select2::widget([
//                    'value' => Yii::$app->request->get('userGivingLovestars'),
//                    'name' => 'userGivingLovestars',
//                    'data' => ArrayHelper::map(User::find()->all(),'id','full_name'),
//                    'options' => ['placeholder' => 'Select'],
//                    'pluginOptions' => [
//                      'allowClear' => true
//                    ],
//                  ]);
//                ?>
<!--              </div>-->
<!---->
<!--              <div class="col-md-2">-->
<!--                <label for="rank">Teacher Good</label>-->
<!--                --><?php
//                  echo Select2::widget([
//                    'value' => Yii::$app->request->get('teacherGivingValue'),
//                    'name' => 'teacherGivingValue',
//                    'data' => ArrayHelper::map(Teacher::find()->all(),'id','title'),
//                    'options' => ['placeholder' => 'Select Good'],
//                    'pluginOptions' => [
//                      'allowClear' => true
//                    ],
//                  ]);
//                ?>
<!--              </div>-->
<!--  -->
<!--              <div class="col-md-2">-->
<!--                <button type="submit">Search</button>-->
<!--              </div>-->
            </div>
          </form>
        </div>

        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                  'id',
                  [
                    'attribute' => 'userGivingLovestars',
                    'value' => function($data) {
                      $user = User::findOne($data->userGivingLovestars);
                      return !empty($user) ? Html::a($user->full_name, ['user/view', 'id' => $user->id]) : '<span class="not-set">(not set)</span>';
                    },
                    'format' => 'html'
                  ],
                  [
                    'attribute' => 'teacherGivingValue',
                    'value' => function($data) {
                      $teacher = Teacher::findOne($data->teacherGivingValue);
                      return !empty($teacher) ? Html::a($teacher->title, ['user/view', 'id' => $teacher->id]) : '<span class="not-set">(not set)</span>';
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
//                  [
//                      'class' => 'yii\grid\ActionColumn',
//                      'template' => '<div class="icon-action-wrapper">{view}</div><div class="icon-action-wrapper">{update}</div><div class="icon-action-wrapper">{delete}</div>',
//                  ],
                ],
            ]); ?>
        </div>
    </div>
</div>
