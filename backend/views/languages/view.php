<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Languages */

$this->title = 'Language: ' . $model->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="languages-view box box-warning">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete the language?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Back', ['index'] ,['class' => 'btn btn-warning']) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
              'id',
              'title',
              'code',
              [
                'attribute' => 'status',
                'value' => function($data) {
                  return empty($data->status) ? '<span class="not-set">(not set)</span>' : \app\models\Languages::$statuses[$data->status];
                },
                'format' => 'html',
              ],
            ],
        ]) ?>
    </div>
</div>
