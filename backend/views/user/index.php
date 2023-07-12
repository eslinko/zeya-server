<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\models\Teacher;
use app\models\Partner;

/* @var $this yii\web\View */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index box">

    <div class="box-header">
        <h1><?= Html::encode($this->title) ?></h1>
    </div>

    <div class="box-body">
        <p>
            <?= Html::a('Create New', ['create'], ['class' => 'btn btn-success']) ?>
        </p>

        <div class="search-form" style="float: none;width: 100%;margin: 20px 0;">
          <form method="get" action="<?= \yii\helpers\Url::to(['user/index']) ?>">
            <div class="row">

              <div class="col-md-2">
                <label for="rank">Username</label>
                <?= Html::input(
                  'text',
                  'username',
                  !empty(Yii::$app->request->get('username')) ? Yii::$app->request->get('username') : '',
                  ['class' => 'form-control']
                ) ?>
              </div>

              <div class="col-md-2">
                <label for="rank">Email</label>
                <?= Html::input(
                  'text',
                  'email',
                  !empty(Yii::$app->request->get('email')) ? Yii::$app->request->get('email') : '',
                  ['class' => 'form-control']
                ) ?>
              </div>
              
              <div class="col-md-2">
                <label for="rank">Full name</label>
                <?= Html::input(
                  'text',
                  'full_name',
                  !empty(Yii::$app->request->get('full_name')) ? Yii::$app->request->get('full_name') : '',
                  ['class' => 'form-control']
                ) ?>
              </div>

              <div class="col-md-2">
                <label for="rank">Role</label>
                <select class="form-control" name="role" id="role">
                  <option value="" <?php echo empty(Yii::$app->request->get('role')) ? 'selected' : ''?>>All</option>
                  <option value="admin"
					          <?php if(!empty(Yii::$app->request->get('role')) && Yii::$app->request->get('role') === 'admin') echo ' selected'?>
                  >Admin</option>
                  <option value="user"
					          <?php if(!empty(Yii::$app->request->get('role')) && Yii::$app->request->get('role') === 'user') echo ' selected'?>
                  >User</option>
                </select>
              </div>
              
              <div class="col-md-2">
                <label for="rank">Teacher</label>
                <?php
                  echo Select2::widget([
                    'value' => Yii::$app->request->get('teacher'),
                    'name' => 'teacher',
                    'data' => ArrayHelper::map(Teacher::find()->all(),'id','title'),
                    'options' => ['placeholder' => 'Select Teacher'],
                    'pluginOptions' => [
                      'allowClear' => true
                    ],
                  ]);
                ?>
              </div>


              <div class="col-md-2">
                <label for="rank">Partners</label>
                <?php
                echo Select2::widget([
                  'value' => Yii::$app->request->get('partner'),
                  'name' => 'partner',
                  'data' => ArrayHelper::map(Partner::find()->all(),'id','legalName'),
                  'options' => ['placeholder' => 'Select Partner'],
                  'pluginOptions' => [
                    'allowClear' => true
                  ],
                ]);
                ?>
              </div>
              
              <div class="col-md-2">
                <button type="submit">Search</button>
              </div>
            </div>
          </form>
        </div>

        <div class="table-responsive">
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
//                    ['class' => 'yii\grid\SerialColumn'],
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
                    [
                      'attribute' => 'currentLovestarsCounter',
                      'value' => function($data) {
                        return $data->currentLovestarsCounter ?  $data->currentLovestarsCounter : '0';
                      },
                      'format' => 'html'
                    ],
                    [
                        'attribute' => 'status',
                        'value' => function($data) {
                            if( $data->status == 10 ) return '<span class="label label-success">Active</span>';
                            else return '<span class="label label-default">Blocked</span>';
                        },
                        'format' => 'html'
                    ],
                    'role',
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
                      'attribute' => 'verifiedUser',
                      'value' => function($data) {
                        return $data->verifiedUser ? '<span class="text-success">Yes</span>' : '<span class="not-set">No</span>';
                      },
                      'format' => 'html'
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'template' => '<div class="icon-action-wrapper">{view}</div><div class="icon-action-wrapper">{update}</div><div class="icon-action-wrapper">{delete}</div>',
                        'buttons' => [
                            'delete' => function ($url) {
                                return Html::a('<span class="glyphicon glyphicon-trash"></span>', $url, [
                                    'title' => 'Deactivate',
                                    'data' => [
                                        'confirm' => 'Deactivate the user?',
                                        'method' => 'post',
                                    ],
                                ]);
                            },
                        ],
                    ],
                ],
            ]); ?>
        </div>
    </div>
</div>
