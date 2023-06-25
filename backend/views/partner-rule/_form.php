<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use \app\models\Partner;

/* @var $this yii\web\View */
/* @var $model app\models\PartnerRule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="partner-rule-form">
    <?php $form = ActiveForm::begin(); ?>

  <div class="form-row">
    <div class="col-md-4">
      <?php echo $form->field($model, 'partnerId')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(Partner::find()->all(),'id','legalName'),
        'options' => [
          'placeholder' => 'Select Partner',
        ],
      ]); ?>
    </div>

    <div class="col-md-4">
		  <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    </div>
    
    <div class="col-md-4">
		  <?= $form->field($model, 'triggerName')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-6">
	    <?= $form->field($model, 'emissionCalculationBaseValue')->textInput(['maxlength' => true]) ?>
    </div>
    
    <div class="col-md-6">
	    <?= $form->field($model, 'emissionCalculationPercentage')->textInput(['maxlength' => true]) ?>
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