<?php

use common\models\User;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invitation-codes-index box">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],
                    'id',
                    [
                        'attribute' => 'user_id',
                        'value' => function ($data) {
                            return empty($data->user_id) ? '<span class="not-set">(not set)</span>' : '<a href="' . Url::to(['user/view', 'id' => $data->user_id]) . '">' . User::getArrWithIdLabel([User::find()->where(['id' => $data->user_id])->asArray()->one()])[$data->user_id] . '</a>';
                        },
                        'format' => 'html',
                    ],
                    'inserted_code',
                    'error_type',
                    [
                        'attribute' => 'timestamp',
                        'value' => function ($data) {
                            return empty($data->timestamp) ? '<span class="not-set">(not set)</span>' : date('Y-m-d H:i:s', $data->timestamp);
                        },
                        'format' => 'html',
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
