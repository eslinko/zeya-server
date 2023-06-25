<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;
use app\models\HashTag;
use app\models\Teacher;

/* @var $this yii\web\View */
/* @var $model app\models\TeacherOutcome */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="teacher-outcome-form">
    <?php $form = ActiveForm::begin(); ?>

  <div class="form-row">
    <div class="col-md-4">
        <?= $form->field($model, 'title')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-4">
		<?php echo $form->field($model, 'teacherId')->widget(Select2::classname(), [
			'data' => ArrayHelper::map(Teacher::find()->all(),'id','title'),
			'options' => [
				'placeholder' => 'Select Teacher'
			],
		]); ?>
    </div>

    <div class="col-md-4">
		<?php echo $form->field($model, 'hashtags')->widget(Select2::classname(), [
			'data' => ArrayHelper::map(HashTag::find()->all(),'id','name'),
			'options' => [
				'placeholder' => 'Select Hashtags',
				'multiple' => true,
			],
		]); ?>
    </div>

    <div class="col-md-4">
		<?php echo $form->field($model, 'type')->widget(Select2::classname(), [
			'data' => \app\models\TeacherOutcome::$types,
			'options' => [
				'placeholder' => 'Select Hashtags',
//				'multiple' => true,
			],
		]); ?>
    </div>
    
    <div class="col-md-4">
		<?php echo $form->field($model, 'valueInLovestarsFrom')->textInput([
            'type' => 'number',
            'min' => 0
    ]); ?>
    </div>
    
    <div class="col-md-4">
    <?php echo $form->field($model, 'valueInLovestarsTo')->textInput([
        'type' => 'number',
        'min' => 0
    ]); ?>
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