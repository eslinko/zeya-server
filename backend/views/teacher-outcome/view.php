<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\HashTag;
use app\models\Teacher;
use app\models\TeacherOutcome;

/* @var $this yii\web\View */
/* @var $model app\models\TeacherOutcome */

$this->title = 'Teacher Outcome: ' . $model->title;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teacher-outcome-view box box-warning">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete the outcome?',
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
            ],
        ]) ?>
    </div>
</div>
