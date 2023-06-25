<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\HashTag;
use app\models\Teacher;
use app\models\TeacherOutcome;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-outcome-index box">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
          <?= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <div class="search-form" style="float: none;width: 100%;margin: 20px 0;">
          <form method="get" action="<?= \yii\helpers\Url::to(['teacher-outcome/index']) ?>">
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
                <label for="rank">Type</label>
                <select class="form-control" name="type" id="type">
                  <option value="" <?php echo empty(Yii::$app->request->get('type')) ? 'selected' : ''?>>All</option>
					        <?php foreach (\app\models\TeacherOutcome::$types as $value => $name):?>
                      <option
                          value="<?php echo $value?>"
                        <?php if(!empty(Yii::$app->request->get('type')) && (int)Yii::$app->request->get('type') === $value) echo ' selected'?>
                                >
                        <?php echo $name?>
                      </option>
					        <?php endforeach;?>
                </select>
              </div>

              <div class="col-md-2">
                <label for="rank">Teacher</label>
                <?php
                  echo Select2::widget([
                    'value' => Yii::$app->request->get('teacherId'),
                    'name' => 'teacherId',
                    'data' => ArrayHelper::map(Teacher::find()->all(),'id','title'),
                    'options' => ['placeholder' => 'Select Teacher'],
                    'pluginOptions' => [
                      'allowClear' => true
                    ],
                  ]);
                ?>
              </div>

              <div class="col-md-2">
                <label for="rank">Hashtags</label>
                <?php
                  echo Select2::widget([
                    'value' => Yii::$app->request->get('hashtags'),
                    'name' => 'hashtags',
                    'data' => ArrayHelper::map(HashTag::find()->all(),'id','name'),
                    'options' => ['placeholder' => 'Select Hashtags'],
                    'pluginOptions' => [
                      'allowClear' => true,
                      'multiple' => true
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
//                  'id',
                  'title',
                  [
                    'attribute' => 'type',
                    'value' => function($data) {
                      return TeacherOutcome::$types[$data->type];
                    },
                    'format' => 'html',
                  ],
                  [
                    'attribute' => 'teacherId',
                    'value' => function($data) {
                      return empty($data->teacherId) ? '<span class="not-set">(not set)</span>' : Teacher::findOne($data->teacherId)->title;
                    },
                    'format' => 'html',
                  ],
                  [
                    'attribute' => 'hashtags',
                    'value' => function($data) {
                      return empty($data->hashtags) ? '<span class="not-set">(not set)</span>' : HashTag::fromIdsToNames($data->hashtags);
                    },
                    'format' => 'html',
                  ],
                  'valueInLovestarsFrom',
                  'valueInLovestarsTo',
                  [
                    'attribute' => 'description',
                    'value' => function($data) {
                      return !$data->description ? '<span class="not-set">(not set)</span>' : $data->description;
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
