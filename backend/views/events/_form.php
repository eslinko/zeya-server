<?php

use app\models\Events;
use common\models\User;
use dosamigos\datetimepicker\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use mihaildev\ckeditor\CKEditor;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model app\models\Events */
/* @var $form yii\widgets\ActiveForm */
?>
<?php if($model->status === 'added') $model->status = 'opened';?>
<div class="events-form">
    <?php $form = ActiveForm::begin(); ?>

  <div class="form-row">
    <div class="col-md-6">
        <?php echo $form->field($model, 'facebook_url')->textInput(['maxlength' => true]) ?>
    </div>

    <div class="col-md-6">
        <?php
        $atts = ['maxlength' => true];
        echo $form->field($model, 'name')->textInput($atts)
        ?>
    </div>

      <div class="col-md-6">
          <?= $form->field($model, 'raw_facebook_date')->textInput(['maxlength' => true]) ?>
      </div>

      <div class="col-md-3">
          <?php
          $atts = [];

          $model->start_timestamp = !empty($model->start_timestamp) ? date('Y-m-d H:i', $model->start_timestamp) : '';
          echo $form->field($model, 'start_timestamp')->widget(DateTimePicker::className(), [
              'options' => $atts
          ])
          ?>
      </div>

      <div class="col-md-3">
          <?php $model->end_timestamp = !empty($model->end_timestamp) ? date('Y-m-d H:i', $model->end_timestamp) : ''; ?>
          <?= $form->field($model, 'end_timestamp')->widget(DateTimePicker::className()) ?>
      </div>

      <div class="col-md-12">
          <!--        (paste screenshot with text address from your buffer)-->
          <?= $form->field($model, 'raw_facebook_place_image')->textInput(['maxlength' => true, 'style' => 'padding-right: 70px;'])->label('Address Screenshot (paste screenshot with text address from your buffer)') ?>
          <img src="<?php echo Yii::getAlias('@web').'/assets/images/image-past.png'?>" alt="" style="position: absolute;width: 20px;right: 39px;top: 32px;">
      </div>

      <div class="col-md-6">
          <?php
          $atts = ['maxlength' => true];
          echo $form->field($model, 'place')->textInput($atts) ?>
      </div>

    <div class="col-md-6">
        <?php
        $atts = ['maxlength' => true];
        echo $form->field($model, 'address')->textInput($atts)->label('Address (processed or entered manually)*') ?>
    </div>

      <div class="col-md-6">
          <?php
          $atts = ['maxlength' => true];
          echo $form->field($model, 'organizer_facebook_title')->textInput($atts)->label('Organiser Facebook Title*') ?>
      </div>

      <div class="col-md-6">
          <?php
          $atts = ['maxlength' => true];
          echo $form->field($model, 'facebook_category')->textInput($atts)->label('Category*') ?>
      </div>

      <div class="col-md-6">
          <?= $form->field($model, 'ticket_url')->textInput(['maxlength' => true]) ?>
      </div>

      <div class="col-md-3">
          <?php echo $form->field($model, 'organizer_id')->widget(Select2::classname(), [
              'data' => User::getArrWithIdLabel(User::find()->all()),
              'options' => [
                  'placeholder' => 'Select Organizer',
                  'multiple' => false,
              ],
          ]); ?>
      </div>

      <div class="col-md-3">
          <?php echo $form->field($model, 'status' )->textInput(['readonly' => true, 'style' => 'text-transform: uppercase'])->label('Status (automatic)') ?>
      </div>

      <div class="col-md-12">
          <?php
          echo $form->field($model, 'description')->widget(CKEditor::className(),[
              'editorOptions' => [
                  'preset' => 'full',
                  'inline' => false,
                  'height' => 300
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
<script>

    $('#events-start_timestamp').removeAttr('readonly')

    if('<?php echo $model->status?>' === 'added' || '<?php echo $model->status?>' === 'opened') {
        const url = '/admin/events/set-status-opened-to-event';
        $.ajax({
            url: url,
            data: {
                event_id: '<?php echo $model->id?>'
            },
            method: 'POST',
            success: function (res) {
                console.log(res);
            },
            error: function (d) {
                console.log(d);
            }
        });
    }

    $('body').on('change', '#events-raw_facebook_date', function (e) {
        e.preventDefault();

        const $this = $(this);
        const val = $this.val()
        const url = '/admin/events/get-timestamp-from-facebook-event-date-string';

        $.ajax({
            url: url,
            data: {
                date_string: val
            },
            method: 'POST',
            success: function (res) {
                if(res.date_start !== undefined && res.date_start !== false && res.date_start.length) {
                    $('#events-start_timestamp').val(res.date_start)
                }
                if(res.date_end !== undefined && res.date_end !== false && res.date_end.length) {
                    $('#events-end_timestamp').val(res.date_end)
                }
            },
            error: function (d) {
                console.log(d);
            }
        });

    });

    // смотрит была ли вставка картинки
    document.querySelector('#events-raw_facebook_place_image').addEventListener('paste', function(e) {
        e.preventDefault()
        const clipboardData = e.clipboardData || window.clipboardData;
        for(let i = 0; i < clipboardData.items.length; i++) {
            if(i === 1) return;
            const item = clipboardData.items[i];

            // Check if the clipboard item is an image
            if(item.type.indexOf("image") != -1) {

                if(confirm('Try to pull the address out of the picture?')) {
                    $('body').addClass('loader')
                    // Get the image as a Blob
                    var blob = item.getAsFile();

                    // Create a new FormData object
                    var formData = new FormData();

                    // Add the image file to the form data
                    formData.append("fileToUpload", blob, "pastedImage.png");

                    $.ajax({
                        url: '/admin/events/get-address-from-image',
                        data: formData,
                        method: 'POST',
                        contentType: false,
                        processData: false,
                        success: function (res) {
                            if(res !== undefined && res !== false) {
                                $('body').find('#events-address').val(res.formatted_address)
                                $('body').find('#events-place').val(res.geometry.location.lat + ',' + res.geometry.location.lng)
                            }
                        },
                        error: function (d) {
                            console.log(d);
                        },
                        complete: function(d) {
                            $('body').removeClass('loader')
                        }
                    });
                }
            }

        }

    });
</script>