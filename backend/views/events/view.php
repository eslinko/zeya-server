<?php

use app\models\Events;
use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Events */

$this->title = 'Event ID: ' . $model->id;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="events-view box box-warning">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete the event?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Back', ['index'], ['class' => 'btn btn-warning']) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'facebook_url',
                'name',
                'raw_facebook_date',
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
                'raw_facebook_place_image',
                'place',
                'address',
                'facebook_category',
                'ticket_url',
                [
                    'attribute' => 'status',
                    'value' => function ($data) {
                        return Events::$statuses[$data->status];
                    },
                    'format' => 'html',
                ],
                [
                    'attribute' => 'organizer_id',
                    'value' => function ($data) {
                        return empty($data->organizer_id) ? '<span class="not-set">(not set)</span>' : '<a href="' . \yii\helpers\Url::to(['user/view', 'id' => $data->organizer_id]) . '">' . User::getArrWithIdLabel([User::find()->where(['id' => $data->organizer_id])->asArray()->one()])[$data->organizer_id] . '</a>';
                    },
                    'format' => 'html',
                ],
                [
                    'attribute' => 'description',
                    'value' => function ($data) {
                        return !$data->description ? '<span class="not-set">(not set)</span>' : $data->description;
                    },
                    'format' => 'html',
                ],
                'description_langs',
            ],
        ]) ?>
    </div>
</div>
