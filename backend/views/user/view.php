<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->params['breadcrumbs'][] = ['label' => 'Users', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-view box box-warning">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('Edit', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a('Change Password', ['update-pass', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
            <?= Html::a('Delete', ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => 'Are you sure you want to delete this user?',
                    'method' => 'post',
                ],
            ]) ?>
            <?= Html::a('Back', ['settings/user'], ['class' => 'btn btn-warning']) ?>
        </p>

        <div class="table-responsive">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    'id',
                    'username',
                    'full_name',
                    'publicAlias',
                    [
                      'attribute' => 'telegram',
                      'value' => function($data) {
                        return $data->telegram ?  $data->telegram: '<span class="not-set">(not set)</span>';
                      },
                      'format' => 'html'
                    ],
                    [
                        'attribute' => 'email',
                        'value' => function($data) {
                            return $data->email ?  $data->email: '<span class="not-set">(not set)</span>';
                        },
                        'format' => 'html'
                    ],
                    //'auth_key',
                    //'password_hash',
                    //'password_reset_token',
                    [
                        'attribute' => 'status',
                        'value' => function($data) {
                            if( $data->status == 10 ) return '<span class="label label-success">Active</span>';
                            else return '<span class="label label-default">Blocked</span>';
                        },
                        'format' => 'html'
                    ],
                    [
                        'attribute' => 'role',
                        'value' => function($data) {
                            return \common\models\User::ROLES[$data->role];
                        },
                        'format' => 'html'
                    ],
                    [
                      'attribute' => 'teacher',
                      'value' => function($data) {
                        $teachers = \app\models\User2Teacher::getAllTeachersByUserId($data->id);
                          $res = [];
                          if(!empty($teachers)) {
                              foreach ($teachers as $teacher) {
                                  $res[] = Html::a($teacher['title'], ['teacher/view', 'id' => $teacher['id']]);
                              }
                              $res = implode($res, ', ');
                          } else $res = '<span class="not-set">(not set)</span>';

                        return $res;
                      },
                      'format' => 'html'
                    ],
                    [
                      'attribute' => 'partners',
                      'value' => function($data) {
                        $partners = \app\models\User2Partner::getPartnersByUserId($data->id);
                        $res = [];
                        if(!empty($partners)) {
                          foreach ($partners as $partner) {
							              $res[] = Html::a($partner->legalName, ['partner/view', 'id' => $partner->id]);
                          }
                          $res = implode($res, ', ');
                        } else $res = '<span class="not-set">(not set)</span>';
                        
                        return $res;
                      },
                      'format' => 'html'
                    ],
                    [
                      'attribute' => 'currentLovestarsCounter',
                      'value' => function($data) {
                        return $data->currentLovestarsCounter ?  $data->currentLovestarsCounter : '0';
                      },
                      'format' => 'html'
                    ],
                    [
                      'attribute' => 'verifiedUser',
                      'value' => function($data) {
                        return $data->verifiedUser ? '<span class="text-success">Yes</span>' : '<span class="not-set">No</span>';
                      },
                      'format' => 'html'
                    ],
                    'language',
                ],
            ]) ;
            ?>
        </div>

    </div>

</div>
