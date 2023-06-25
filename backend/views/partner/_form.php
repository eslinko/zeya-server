<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model app\models\Partner */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="partner-form">
    <?php $form = ActiveForm::begin(); ?>

  <div class="form-row">
    <div class="col-md-6">
        <?= $form->field($model, 'legalName')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-6">
		<?= $form->field($model, 'billingVATNumber')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-12">
		<?php echo $form->field($model, 'billingDetails')->widget(CKEditor::className(),[
			'editorOptions' => [
				'preset' => 'basic',
				'inline' => false,
			],
		]);?>
    </div>
    
    <div class="col-md-12">
		  <?php echo $form->field($model, 'description')->widget(CKEditor::className(),[
		   'editorOptions' => [
			   'preset' => 'basic',
			   'inline' => false,
		   ],
	   ]);?>
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