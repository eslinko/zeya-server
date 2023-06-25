<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$items = [
    ''  => '',
    0 => 'Blocked',
    10 => 'Active'
];

$role = \common\models\User::ROLES;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form" id="formUpdPass">

    <?php $form = ActiveForm::begin(); ?>

    <div class="form-row">

        <div class="col-md-12">
            <?= $form->field($model, 'password_hash')->passwordInput(['maxlength' => true, 'value' => '']) ?>
        </div>

<!--        <div class="col-md-6">-->
            <?//= $form->field($model, 'confirmPass')->passwordInput(['maxlength' => true, 'value' => '']) ?>
<!--        </div>-->

    </div>


    <div class="form-group text-center">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
        <?= Html::a('Cancel', ['settings/user/view/'.$model->id], ['class' => 'btn btn-danger']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    $(document).ready(function () {

//        $('#w0 #user-confirmpass').attr('aria-required', true);
//
//        $('form#w0').submit(function(e){
//           setTimeout(function () {
//               if($('#w0 #user-confirmpass').val().length < 1){
//                   $('#user-confirmpass').parent().removeClass('has-success').addClass('has-error');
//                   return false;
//               }
//
//               if( $('#w0 .has-error').length < 1 ) {
//                   alert('ok');
//                   $('form#w0').submit();
//               }
//           }, 500);
//
//        });

//        $('#formUpdPass #user-confirmpass').change(function() {
//            var pass = $('#user-password_hash').val();
//            var conf_pass = $('#user-confirmpass').val();
//
//            if (pass != conf_pass) {
//                $('#user-confirmpass').parent().removeClass('has-success').addClass('has-error');
//
//            } else {
//                $('#user-confirmpass').parent().removeClass('has-success').addClass('has-error');
//            }
//        });

    });
</script>
