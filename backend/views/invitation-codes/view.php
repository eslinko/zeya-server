<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\InvitationCodes */

$this->title = 'Code: ' . $model->code;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="invitation-codes-view box box-warning">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete the code?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Back', ['index'] ,['class' => 'btn btn-warning']) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
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
                        return empty($data->registered_user_id) ? '<span class="not-set">(not set)</span>' : '<a href="' . Url::to(['user/view', 'id' => $data->registered_user_id]) . '">' . User::getArrWithIdLabel([User::find()->where(['id' => $data->registered_user_id])->asArray()->one()])[$data->registered_user_id] . '</a>';
                    },
                    'format' => 'html',
                ],
                'signup_date',
            ],
        ]) ?>
    </div>
</div>
