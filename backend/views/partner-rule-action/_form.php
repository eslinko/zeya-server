<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use \app\models\PartnerRule;
use \common\models\User;

/* @var $this yii\web\View */
/* @var $model app\models\PartnerRuleAction */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="partner-rule-form">
    <?php $form = ActiveForm::begin(); ?>

  <div class="form-row">
    <div class="col-md-6">
      <?php echo $form->field($model, 'ruleId')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(PartnerRule::find()->all(),'id','title'),
        'options' => [
          'placeholder' => 'Select Rule',
        ],
      ]); ?>
    </div>

    <div class="col-md-6">
      <?php echo $form->field($model, 'emittedLovestarsUser')->widget(Select2::classname(), [
        'data' => ArrayHelper::map(User::find()->all(),'id','full_name'),
        'options' => [
          'placeholder' => 'Select User',
        ],
      ]); ?>
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