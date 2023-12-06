<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
//use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use backend\models\InvitationCodes;

/* @var $this yii\web\View */
/* @var $model backend\models\InvitationCodes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="invitation-codes-form">
    <?php $form = ActiveForm::begin(); ?>

  <div class="form-row">
    <div class="col-md-6">
        <?= $form->field($model, 'user_id')->widget(Select2::classname(), [
            'data' => User::getArrWithIdLabel(User::find()->all()),
            'options' => [
                'placeholder' => 'Select Owner',
                'multiple' => false,
                'value' => Yii::$app->user->id
            ],
        ]);  ?>
    </div>

    <div class="col-md-6">
		<?php  echo $form->field($model, 'code')->textInput(['maxlength' => true, 'value' => InvitationCodes::generateInvitationCode(), 'readonly' => true, 'style' => 'background-color: #eeeeee!important']) ?>
    </div>

    <div class="form-group" style="text-align: center;">
      <div class="col-md-12">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['index'] ,['class' => 'btn btn-danger']) ?>
      </div>
    </div>

    <?php ActiveForm::end(); ?>
  </div>
</div>