<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\CreativeTypes */

$this->title = 'Partner: ' . $model->type_en;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="creative-types-view box box-warning">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete the creative type?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Back', ['index'] ,['class' => 'btn btn-warning']) ?>
        </p>

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
              'id',
              'type_en',
              'type_ru',
              'type_et',
            ],
        ]) ?>
    </div>
</div>
