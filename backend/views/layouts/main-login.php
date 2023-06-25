<?php
use backend\assets\AppAsset;
use yii\helpers\Html;

/* @var $this \yii\web\View */
/* @var $content string */

dmstr\web\AdminLteAsset::register($this);

$this->registerCssFile('@web/css/site.css',[
    'depends' => [
        \yii\bootstrap\BootstrapPluginAsset::className()
    ]]);

$this->registerJs("
    $(document).ready(function() {
        if($('.login-box-body #login-form div').hasClass('has-error')) {
           $('.login-box-body').removeClass('box-info').addClass('box-danger');
        } else if(!$('.login-box-body').hasClass('box-info')){ 
            $('.login-box-body').removeClass('box-danger').addClass('box-success');
        }
    });
");
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
</head>
    <body class="login-page">
    <div class="fullscreen-bg">
        <?php $this->beginBody() ?>
            <div class="login-box-wrapper">
                <?= $content ?>
            </div>
        <video loop muted autoplay poster="/backend/web/upload/video-bckg/Behind-the-screen.jpg" class="fullscreen-bg__video">
            <source src="/backend/web/upload/video-bckg/Behind-the-screen.mp4" type="video/mp4">
            <source src="/backend/web/upload/video-bckg/Behind-the-screen.webm" type="video/webm">
        </video>
        <?php $this->endBody() ?>
    </div>
</body>
</html>
<?php $this->endPage() ?>
