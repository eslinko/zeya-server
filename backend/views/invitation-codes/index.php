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
        <p>
          <?= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

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
                    'code',
                    [
                        'attribute' => 'registered_user_id',
                        'value' => function ($data) {
                            if(empty($data->registered_user_id))
                                return '<span class="not-set">(not set)</span>';
                            else {
                                $res = User::find()->where(['id' => $data->registered_user_id])->asArray()->one();
                                if($res == NULL) return '<span class="not-set">(not set)</span>';
                                if(!isset(User::getArrWithIdLabel([$res])[$data->registered_user_id])) return '<span class="not-set">(not set)</span>';
                                return '<a href="' . Url::to(['user/view', 'id' => $data->registered_user_id]) . '">' . User::getArrWithIdLabel([$res])[$data->registered_user_id] . '</a>';
                            }
                            //return empty($data->registered_user_id) ? '<span class="not-set">(not set)</span>' : '<a href="' . Url::to(['user/view', 'id' => $data->registered_user_id]) . '">' . User::getArrWithIdLabel([User::find()->where(['id' => $data->registered_user_id])->asArray()->one()])[$data->registered_user_id] . '</a>';
                        },
                        'format' => 'html',
                    ],
                    [
                        'attribute' => 'signup_date',
                        'value' => function ($data) {
                            return empty($data->signup_date) ? '<span class="not-set">(not set)</span>' : date('m/d/Y', $data->signup_date);
                        },
                        'format' => 'html',
                    ],
                  [
                      'class' => 'yii\grid\ActionColumn',
                      'template' => '<div class="icon-action-wrapper">{view}</div><div class="icon-action-wrapper">{delete}</div>',
                  ],
                ],
            ]); ?>
        </div>
    </div>
</div>
