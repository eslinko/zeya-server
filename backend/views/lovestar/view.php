<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\HashTag;

/* @var $this yii\web\View */
/* @var $model app\models\Teacher */

$this->title = 'Teacher: ' . $model->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-view box box-warning">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete the teacher?',
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
              [
                'attribute' => 'description',
                'value' => function($data) {
                  return !$data->description ? '<span class="not-set">(not set)</span>' : $data->description;
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
            ],
        ]) ?>
    </div>
</div>
