<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use app\models\Teacher;
use common\models\User;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="teaching-transaction-index box">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
<!--      <p>-->
<!--		    --><?php //= Html::a('Add New', ['create'], ['class' => 'btn btn-success']) ?>
<!--      </p>-->
      


        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                  'id',
                  [
                    'attribute' => 'name',
                    'value' => 'name',
                    'format' => 'text'
                  ],
                  [
                    'attribute' => 'value',
                    'value' =>  'value',
                    'format' => 'text'
                  ],
/*                    [

                     'class' => 'yii\grid\CheckboxColumn',
                     'checkboxOptions' => function($model) {
                        return ['checked' => $model->value === 'true' ? true : false,
                            'disabled' => true];
                    }
                    ],*/
/*                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '{UpdateButton}',  // the default buttons + your custom button
                        'urlCreator' => function($action, $model, $key, $index) {
                            return Url::toRoute(['settings/update-setting','name' => $model->name, 'value' => 'fff']);
                        },
                        'buttons' => [
                            'UpdateButton' => function($url, $model, $key) {     // render your custom button
                                return Html::submitButton('Save', ['class' => 'btn btn-success']);
                            }
                        ]
                    ],*/
                    [
                        //'label' => 'View Profile',

                        'content' => function($model) {
                            if($model->value === 'true')
                                $change = 'false';
                            else
                                $change = 'true';
                            return Html::a('Change', ['settings/update-setting', 'name' =>$model->name, 'value' => $change],
                                ['class' => 'btn btn-primary']);
                        }
                    ]
//                  [
//                      'class' => 'yii\grid\ActionColumn',
//                      'template' => '<div class="icon-action-wrapper">{view}</div><div class="icon-action-wrapper">{update}</div><div class="icon-action-wrapper">{delete}</div>',
//                  ],
                ],
            ]); ?>
        </div>
    </div>
</div>
