<?php

use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use app\models\Events;
use yii\helpers\Url;

//use dosamigos\datetimepicker\DateTimePicker;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="events-index box">
    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <div class="search-form" style="float: none;width: 100%;margin: 20px 0;">
            <form method="get" action="<?= Url::to(['events/index']) ?>">
                <div class="row">
                    <div class="col-md-2">
                        <label for="rank">Facebook Event URL</label>
                        <?= Html::input(
                            'text',
                            'facebook_url',
                            !empty(Yii::$app->request->get('facebook_url')) ? Yii::$app->request->get('facebook_url') : '',
                            ['class' => 'form-control']
                        ) ?>
                    </div>

                    <div class="col-md-3">
                        <label for="rank">Event name</label>
                        <?= Html::input(
                            'text',
                            'name',
                            !empty(Yii::$app->request->get('name')) ? Yii::$app->request->get('name') : '',
                            ['class' => 'form-control']
                        ) ?>
                    </div>

                    <div class="col-md-1">
                        <label for="open_by_id">Event ID</label>
                        <?= Html::input(
                            'text',
                            'open_by_id',
                            !empty(Yii::$app->request->get('open_by_id')) ? Yii::$app->request->get('open_by_id') : '',
                            ['class' => 'form-control']
                        ) ?>
                    </div>

                    <div class="col-md-2">
                        <label for="rank">Organizer</label>
                        <?php
                        echo Select2::widget([
                            'value' => Yii::$app->request->get('organizer_id'),
                            'name' => 'organizer_id',
                            'data' => User::getArrWithIdLabel(User::find()->all()),
                            'options' => ['placeholder' => 'Select Organizer'],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'multiple' => true
                            ],
                        ]);
                        ?>
                    </div>

                    <?php //if(Yii::$app->user->identity->role !== User::ROLE_EVENT_PROCESSOR):?>
                    <div class="col-md-2">
                        <label for="status">Status</label>
                        <select class="form-control" name="status" id="status">
                            <option value="" <?php echo empty(Yii::$app->request->get('status')) ? 'selected' : '' ?>>
                                All
                            </option>
                            <?php foreach (Events::$statuses as $key => $label): ?>
                                <option value="<?php echo $key ?>"
                                    <?php if (!empty(Yii::$app->request->get('status')) && Yii::$app->request->get('status') === $key) echo ' selected' ?>
                                ><?php echo $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <?php //endif;?>

                    <div class="col-md-1">
                        <button type="submit">Search</button>
                    </div>
                </div>
<!--                <div class="row" style="margin-top: 15px">-->

                    <!--          <div class="col-md-2">-->
                    <!--                  <label for="open_by_id">Start Date</label>-->
                    <!--                  --><?php //echo DateTimePicker::widget([
                    //                      'name'  => 'start_timestamp',
                    //                      'value'  => !empty(Yii::$app->request->get('start_timestamp')) ? Yii::$app->request->get('start_timestamp') : '',
                    ////                        'attribute' => 'created_at',
                    ////                        'language' => 'es',
                    //                      'size' => 'ms',
                    //                      'clientOptions' => [
                    //                          'autoclose' => true,
                    ////                            'format' => 'dd MM yyyy - HH:ii P',
                    //                          'todayBtn' => true
                    //                      ]
                    //                  ]);?>
                    <!--              </div>-->
                    <!---->
                    <!--            <div class="col-md-2">-->
                    <!--                <label for="open_by_id">End Date</label>-->
                    <!--                --><?php //echo DateTimePicker::widget([
                    //                    'name'  => 'end_timestamp',
                    //                    'value'  => !empty(Yii::$app->request->get('end_timestamp')) ? Yii::$app->request->get('end_timestamp') : '',
                    ////                        'attribute' => 'created_at',
                    ////                        'language' => 'es',
                    //                    'size' => 'ms',
                    //                    'clientOptions' => [
                    //                        'autoclose' => true,
                    ////                            'format' => 'dd MM yyyy - HH:ii P',
                    //                        'todayBtn' => true
                    //                    ]
                    //                ]);?>
                    <!--            </div>-->

                    <!--              <div class="col-md-2">-->
                    <!--                <button type="submit">Search</button>-->
                    <!--              </div>-->
<!--                </div>-->
            </form>
        </div>

        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'id',
                    [
                        'attribute' => 'facebook_url',
                        'value' => function ($data) {
                            return empty($data->facebook_url) ? '<span class="not-set">(not set)</span>' : '<div title="' . $data->facebook_url . '" style="max-width: 400px;text-overflow: ellipsis;overflow: hidden;"><a href="' . Url::to(['events/update', 'id' => $data->id]) . '">' . $data->facebook_url . '</a></div>';
                        },
                        'format' => 'raw',
                    ],
                    'name',
                    'facebook_category',
                    'place',
                    [
                        'attribute' => 'organizer_id',
                        'value' => function ($data) {
                            return empty($data->organizer_id) ? '<span class="not-set">(not set)</span>' : '<a href="' . Url::to(['user/view', 'id' => $data->organizer_id]) . '">' . User::getArrWithIdLabel([User::find()->where(['id' => $data->organizer_id])->asArray()->one()])[$data->organizer_id] . '</a>';
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function ($data) {
                            return Events::$statuses[$data->status];
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'start_timestamp',
                        'value' => function ($data) {
                            return empty($data->start_timestamp) ? '<span class="not-set">(not set)</span>' : date('Y-m-d H:i',$data->start_timestamp);
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'end_timestamp',
                        'value' => function ($data) {
                            return empty($data->end_timestamp) ? '<span class="not-set">(not set)</span>' : date('Y-m-d H:i',$data->end_timestamp);
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
