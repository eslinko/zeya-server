<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\HashTag */

$this->title = 'Hashtag: ' . $model->name;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hashtag-view box box-warning">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete the hashtag?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Back', ['index'] ,['class' => 'btn btn-warning']) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
              'id',
              'name',
            ],
        ]) ?>
    </div>
</div>
